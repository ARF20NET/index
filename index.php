<?php
	function getDirList($dir) {
		// array to hold return value
		$retval = [];

		// add trailing slash if missing
		if(substr($dir, -1) != "/") {
		  $dir .= "/";
		}

		// open pointer to directory and read list of files
		$d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
		while(FALSE !== ($entry = $d->read())) {
		  // skip hidden files
		  if($entry[0] == ".") continue;
		  if(is_dir("{$dir}{$entry}")) {
			$retval[] = [
			  'name' => "{$dir}{$entry}/",
			  'type' => filetype("{$dir}{$entry}"),
			  'size' => 0,
			  'lastmod' => filemtime("{$dir}{$entry}")
			];
		  } elseif(is_readable("{$dir}{$entry}")) {
			$retval[] = [
			  'name' => "{$dir}{$entry}",
			  'type' => mime_content_type("{$dir}{$entry}"),
			  'size' => 0, //filesize("{$dir}{$entry}"),
			  'lastmod' => filemtime("{$dir}{$entry}")
			];
		  }
		}
		$d->close();

		return $retval;
	}
	
	function cmp($a, $b) {
		return $a["lastmod"] < $b["lastmod"];
	}
	
	function getlastmodifieddirname($dir) {
		$list = getDirList($dir);
		
		usort($list, "cmp");
		
		//print_r($list[0]);
		echo basename($list[0]["name"]);
		//if ($list[0]["type"] == "dir") getlastmodifiedfilename($list[0]["name"]);
		//else echo basename($list[0]["name"]);
	}
	
	function getlastmodifiedsubdirname($dir) {
		$prelist = getDirList($dir);
		$list = array();
		foreach ($prelist as &$item)
			if ($item["type"] == "dir") array_push($list, $item);
		usort($list, "cmp");
		
		if ($list[0]["type"] == "dir") {
			echo basename($list[0]["name"])."/";
			getlastmodifieddirname($list[0]["name"]);
		}
	}
		
	function getlastmodifiedfilename($dir) {
		/*$list = getDirList($dir);
		
		usort($list, "cmp");
		
		//print_r($list[0]);
		//echo basename($list[0]["name"]);
		if ($list[0]["type"] == "dir") getlastmodifiedfilename($list[0]["name"]);
		else echo basename($list[0]["name"]);*/
		
		$line = shell_exec('ls -Rtl --quoting-style=shell-always '.$dir.' | grep "^[-]" | head -1 | sed "s/.$//" | rev | cut -f1 -d"\'" | rev');
		echo $line;
	}

	function getStreams() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, "http://arf20.com/hls/");
		curl_setopt($ch, CURLOPT_SSH_COMPRESSION, true);
		curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => "https://arf20.com/hls/"
		]);
		$result = curl_exec($ch);

		$streams = array();

		$line = strtok($result, "\n");
		while ($line !== false) {
			$line = strtok("\n");
			if (!strpos($line, ".m3u8")) continue;
			$p = strpos($line, "href=\"") + 6;
			array_push($streams, substr($line, $p, strpos($line, ".m3u8") - $p));
			//array_push($streams, $line);
		}

		return $streams;
	}
	
	header("Onion-Location: http://3fkycvcng6p3etyikxuytavkx2rb2wibvzpfarkixzwqyyjm7lzh7zqd.onion/");
?>

<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8">
		</meta content="ARFNET" property="og:site_name" />
		</meta content="website" property="og:type" />
		</meta content="https://arf20.com/" property="og:url" />
		</meta content="ARFNET Home" property="og:title" />
		</meta content="The arf network, the only updated site backwards compatible with the original Web." property="og:description" />
		</meta content="http://arf20.com/arfnet_logo.png" property="og:image" />
		
		<link rel="shortcut icon" href="/favicon.ico" />
		
		<link rel="stylesheet" type="text/css" href="./style.css">
		
        <title>ARFNET</title>
		<style>
			.title {
				font-size: 36px;
			}
			
			header *{
				display: inline-block;
			}
			
			*{
				vertical-align: middle;
				max-width: 100%;
			}
			
			.live {
				background-color: green;
				display: inline-block;
				margin-left: 20px;
			}
			
			.nolive {
				background-color: red;
				display: inline-block;
				margin-left: 20px;
			}

			<?php 
				if ($_GET["theme"] == "dark") {
					echo "body { background-image: url(\"tile2.gif\"); background-repeat: repeat; } ";
					echo "h1,h2,h3,h4,span { color: white; } ";
				}
			?>
		</style>
    </head>

    <body>
		<header>
			<img src="arfnet_logo.png" width="64">
			<span class="title"><strong>ARFNET Home</strong></span>
		</header>
		<h3>The arf network, the only diverse service provider backwards compatible with the original Internet.<br>
		<a href="https://discord.gg/jy6AjN9ACP">Discord</a>
		<a href="/latin.php">Now in Latin</a>
		<a href="?theme=dark">Dark theme</a></h3>
		<hr>
		<h2>Index</h2>
		<a class="home" href="/">Home</a><br>
			<a class="sec" href="/about.html">About ARFNET</a>
				<a class="sec" href="/design.html">Design Philosophy</a>
				<a class="sec" href="/gallery">Gallery</a>
				<a class="sec" href="https://www.youtube.com/watch?v=lbsce1DniQA&list=PLhWQL9gpbCPb8JNtOFo760GUD4ekXiR-9">Youtube</a>
				<a class="sec" href="https://github.com/ARF20NET">GitHub</a>
				<a class="sec" href="/contact">Contact</a><br>
			<a class="sec" href="/me/">About me</a><br>
			<a class="sec" href="/iservices.php">Internet Services</a>
				<a class="sec" href="/speedtest">Speedtest</a><br>
			<a class="sec" href="/ppservices.php">Public & Private Services</a>
				<a class="sec" href="/customers">Customers</a>
				<a class="sec" href="/donate.php">Donate</a><br>
		<a class="trd" href="/arfCloud/login.php">arfCloud</a><br>
		<a class="sec" href="/bulletin/">Bulletin and updates</a><br>
		<a class="sec" href="/stream">Stream</a><?php
			$streams = getStreams();
			if (sizeof($streams) > 0) {
				echo "<div class=\"live\"><span>Live NOW: ";
				foreach (getStreams() as $stream) {
					echo $stream.' ';
				}
				echo "</span></div>";
			}
			?><br>
		<a class="sec" href="/FTPServer/?C=M&O=D">File Server (random shit)</a>
			<a class="sec" href="/search">Search</a>
			<a class="sec" href="/dmcarequest">DMCA Request</a><br>
				<a class="trd" href="/memes">ah yes, memes</a><br>
				<a class="trd" href="/FTPServer/books">Books</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/books"); ?></span><br>
				<a class="trd" href="/FTPServer/music">Music</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/music"); ?></span><br>
				<a class="trd" href="/FTPServer/films">Films</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/films"); ?></span><a class="latest" href="/player">Player</a><br>
					<a class="frh" href="/FTPServer/films/STAR%20WARS">Star Wars</a><br>
				<a class="trd" href="/FTPServer/series">Series</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/series"); ?></span><br>
					<a class="frh" href="/FTPServer/series/Doctor%20Who">Doctor Who</a><br>
				<a class="trd" href="/FTPServer/OS">OS</a><span class="latest"><?php getlastmodifiedsubdirname("/d/FTPServer/OS"); ?></span><br>
				<a class="trd" href="/FTPServer/software">Software</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/software/amd64-win"); ?></span><br>
				<a class="trd" href="/FTPServer/torrents">Torrents</a><span class="latest"><?php getlastmodifiedfilename("/d/FTPServer/torrents"); ?></span><br>
				<a class="trd" href="/FTPServer/leaks">Leaks</a><span class="latest"><?php getlastmodifieddirname("/d/FTPServer/leaks"); ?></span><br>
		<a class="sec" href="/source?C=M&O=D">Old C/C++/C# Windows VS repository archive</a><br>
		<a class="sec" href="/FTPServer/java">Old java repository archive</a><br>
		<a class="sec" href="/FTPServer/distribution">Distributions</a><br>
		<a class="sec" href="astro.html">Astrophotography section</a><br>
		<a class="sec" href="/satimgview">NOAA Ground Station Image Viewer (broken)</a><br>
		<a class="sec" href="/webring.html">Webring</a><br>

		<hr>
		<span>Last modification 12-03-2023. Estabished somewhere around 2020. Sysadmin: arf20. Contact: <a target="_blank" href="mailto:arf20@arf20.com">arf20@arf20.com</a> arf20#6509 Murcia, Spain.</span><br>
		<a href="/source/LICENSE.txt">Everything in this server by default is published under the GNU General Public License version 3.0</a>
		<a href="/">Copyright &copy; <?php echo date('Y'); ?> ARFNET, LLC.</a>
		<span class="counterborder"> Access counter: <?php include("counter.php"); echo IncrementCounter(); ?> </span><br>
		<img src="gifbuttons/bestvw.gif">
		<img src="gifbuttons/vim.gif">
		<img src="gifbuttons/powered-by-nginx.gif">
		<img src="gifbuttons/powered-by-debian.gif">
		<img src="gifbuttons/powered-by-opnsense.png">
		<img src="gifbuttons/powered-by-proxmox.png">
		<img src="gifbuttons/piracy.gif">
		<img src="gifbuttons/gay.gif">
		<img src="gifbuttons/gplv3.gif">
    </body>
</html>
