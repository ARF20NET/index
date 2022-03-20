<?php
	$live = false;
	if (file_exists("/mnt/hls/arf20.m3u8"))
		$live = true;
		
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
?>

<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8">
		<meta property="og:site_name" content="ARFNET" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="https://arf20.com/" />
		<meta property="og:title" content="ARFNET Home" />
		<meta property="og:description" content="The arf network, the only updated site backwards compatible with the original Web." />
		<meta property="og:image" content="http://arf20.com/arfnet_logo.png" />
		
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
		</style>
    </head>

    <body>
		<header>
			<img src="arfnet_logo.png" width="64">
			<span class="title"><strong>ARFNET Home</strong></span>
		</header>
		<h3>The arf network, the only diverse service provider backwards compatible with the original Internet. <a href="https://discord.gg/jy6AjN9ACP">Discord</a>  <a href="/latin.php">Now in Latin</a></h2>
		<hr>
		<h2>Index</h2>
		<a class="home" href="/">Home</a><br>
			<a class="sec" href="/about.html">About ARFNET</a><a class="sec" href="/design.html">Design Philosophy</a><a class="sec" href="/gallery">Gallery</a><br>
			<a class="sec" href="/me/">About me</a><br>
			<a class="sec" href="/iservices.php">Internet Services</a>
				<a class="sec" href="/speedtest">Speedtest</a><br>
			<a class="sec" href="/ppservices.php">Public & Private Services</a>
				<a class="sec" href="/customers">Customers</a>
				<a class="sec" href="/donate.php">Donate</a><br>
		<a class="trd" href="/arfCloud/login.php">arfCloud</a><br>
		<a class="sec" href="/bulletin/">Bulletin and updates</a><br>
		<a class="sec" href="/stream">Stream</a><div class="<?php if ($live) echo "live"; else echo "nolive"; ?>"><span><?php if ($live) echo "On Live NOW"; else echo "Not live"; ?></span></div><br>
		<a class="sec" href="/FTPServer/?C=M&O=D">File Server (random shit)</a>
			<a class="sec" href="/search">Search</a>
			<a class="sec" href="/piracydisclaimer.txt">Piracy Disclaimer</a>
			<a class="sec" href="/dmcarequest">DMCA Request</a><br>
				<a class="trd" href="/memes">ah yes, memes</a><br>
				<a class="trd" href="/FTPServer/books">Books</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/books"); ?></a><br>
				<a class="trd" href="/FTPServer/music">Music</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/music"); ?></a><br>
				<a class="trd" href="/FTPServer/films">Films</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/films"); ?></a><br>
					<a class="frh" href="/FTPServer/films/STAR%20WARS">Star Wars</a><br>
				<a class="trd" href="/FTPServer/series">Series</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/series"); ?></a><br>
					<a class="frh" href="/FTPServer/series/Doctor%20Who">Doctor Who</a><br>
				<a class="trd" href="/FTPServer/OS">OS</a><a class="latest"><?php getlastmodifiedsubdirname("/d/FTPServer/OS"); ?></a><br>
				<a class="trd" href="/FTPServer/software">Software</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/software/amd64-win"); ?></a><br>
				<a class="trd" href="/FTPServer/torrents">Torrents</a><a class="latest"><?php getlastmodifiedfilename("/d/FTPServer/torrents"); ?></a><br>
				<a class="trd" href="/FTPServer/leaks">Leaks</a><a class="latest"><?php getlastmodifieddirname("/d/FTPServer/leaks"); ?></a><br>
		<a class="sec" href="/source?C=M&O=D">C/C++/C# personal repository</a><br>
			<a class="trd" href="/source/ffbrake">ffbrake</a><a class="sec" href="/source/audiofft/index.txt">details</a><br>
			<a class="trd" href="/source/audiofft">audiofft: Simple downrate GUI for ffmpeg</a><a class="sec" href="/source/audiofft/index.txt">details</a><br>
			<a class="trd" href="/source/arftracksat">arftracksat: A multiplataform 3D satellite tracker</a><a class="sec" href="/source/arttracksat/index.txt">details</a><a class="sec" href="https://github.com/arf20/arftracksat">github</a><br>
			<a class="trd" href="/source/arfTCP">arfTCP: A simple and elegant TCP library</a><a class="sec" href="/arfTCPdoc">documentation & refernce</a><br>
			<a class="trd" href="/source/Z80asm">Z80asm: Basic Z80 assembler and linker</a><a class="sec" href="/source/Z80asm/index.txt">details</a><br>
			<a class="trd" href="/source/Z80sim">Z80sim: Basic Z80 simulator and disassembler (in development)</a><a class="sec" href="/source/Z80sim/index.txt">details</a><br>
			<a class="trd" href="/FTPServer/arfOS/kernel">arfOS kernel (do not try this, very early dev stage)</a><br>
			<a class="trd" href="/source/mppianomidi">mppianomidi: A multiplayer piano suite</a><a class="sec" href="/source/mppianomidi/index.txt">details</a><br>
			<a class="trd" href="/source/uvs">uvs: Uncompressed Video Stream Suite (network flooding guaranteed)</a><a class="sec" href="/source/uvs/index.txt">details</a><br>
			<a class="trd" href="/source/WinAPIUI">WinAPIUI: A TLS 1.3 encrypted chat application</a><a class="sec" href="/source/WinAPIUI/desc.txt">details</a><br>
			<a class="trd" href="/source/whoisUI">whoisUI: A powerful GUI whois application</a><a class="sec" href="/source/whoisUI/desc.txt">details</a><br>
			<a class="trd" href="/source/arfNETTALK3">arfNETTALK3: Modernized NETTALK (in development)</a><a class="sec" href="/source/arfNETTALK3/index.txt">details</a><br>
		<a class="sec" href="/java">Java personal repository</a><br>
			<a class="trd" href="/java/randomgame">randomgame</a><br>
		<a class="sec" href="/FTPServer/distribution">Distributions</a><br>
			<a class="trd" href="/FTPServer/builds/OpenSSL/x64">OpenSSL 3.0.0-alpha7-dev Windows static build amd64</a><a class="sec" href="/FTPServer/builds/OpenSSL/x86">x86</a><br>
			<a class="trd" href="/FTPServer/distribution/ffmpeg_3.4_NDI_3.5_amd64_windows.7z">ffmpeg 3.4 with NewTek NDI&reg; 3.5 Windows binary build amd64</a><br>
			<a class="trd" href="/FTPServer/distribution/suscan-amd64-win64-rtl.zip">suscan for Windows</a><br>
			<a class="trd" href="/FTPServer/distribution/SigDigger-and64-win64-rtl.zip">SigDigger for Windows</a><br>
		<a class="sec" href="astro.html">Astrophotography section</a><br>
		<a class="sec" href="/satimgview">NOAA Ground Station Image Viewer</a><br>
		<hr>
		<span>Sysadmin: arf20. Murcia, Spain. Contact: <a target="_blank" href="mailto:aruizfernandez05@gmail.com">aruizfernandez05@gmail.com</a></span>
		<a href="/source/LICENSE.txt">Everything in this server is published under GPL v3 license</a>
		<a href="/">This site design uses arfSites&trade; with copyright &copy; 2021 ARFNET, LLC.</a>
		<span class="counterborder"> Access counter: <?php include("counter.php"); echo IncrementCounter(); ?> </span><br>
		<img src="bestvw.gif"><img src="msie.gif"><img src="powered-by-debian.gif"><img src="vim.gif"><img src="powered-by-nginx.gif">
    </body>
</html>