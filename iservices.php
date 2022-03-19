<?php
$hostname = "arf20.com";

$subnet = "192.168.0.";

if (!isset($_GET["status"])) {
	$_GET["status"] = "false";
}

function printIPAddr() {
	global $hostname;
	print gethostbyname($hostname);
}

function connect_with_timeout($soc, $host, $port, $timeout = 10) {
    $con_per_sec = 100;
    socket_set_nonblock($soc);
    for($i=0; $i<($timeout * $con_per_sec); $i++) { 
        @socket_connect($soc, $host, $port);
        if(socket_last_error($soc) == SOCKET_EISCONN) { 
            break;
        }
        usleep(1000000 / $con_per_sec);
    }
    socket_set_block($soc);
    return socket_last_error($soc) == SOCKET_EISCONN;
}


function printStatusPort($port) {
	global $hostname;
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	
	$addr = gethostbyname($hostname);
	if ($socket === false) {
		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	} else {
		$result = connect_with_timeout($socket, $addr, $port, 1);
		if ($result === false) {
			echo "<td class=\"offline\">Offline</td>";
		} else {
			echo "<td class=\"online\">Online</td>";
		}
	}
	
	socket_close($socket);
}

function printStatusPortUDP($port) {
	global $hostname;
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	$msg = "hello world";
	
	$addr = gethostbyname($hostname);
	if ($socket === false) {
		echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	} else {
		$result = socket_sendto($socket, $msg, strlen($msg), 0, $addr, $port);
		if ($result === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
		} else {
			$buff = "";
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1,"usec"=>0));
			$result = socket_recvfrom($socket, $buff, 1024, 0, $addr, $port);
			
			if ($result === false) {
				echo "<td class=\"offline\">Offline</td>";
			} else {
				echo "<td class=\"online\">Online</td>";
			}
		}
	}
	
	socket_close($socket);
}

function printHostStatus($ip) {
    $pingresult = exec("/bin/ping -c 1 $ip", $outcome, $status);
    if ($status == 0) {
        echo "<td class=\"online\">Online</td>";
    } else {
        echo "<td class=\"offline\">Offline</td>";
    }
}

ob_end_clean();
ob_start();

function update() {
	ob_flush();
	flush();
}

?>

<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="/style.css">
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
			
			.row {
				display: flex;
			}
			
			.col {
				flex: 33%;
				padding: 40px;
			}
			
			.text {
				margin-left: 20px;
			}
		</style>
    </head>

    <body>
        <header>
			<img src="arfnet_logo.png" width="64">
			<span class="title"><strong>ARFNET</strong></span>
		</header>
		<hr>
		<a class="home" href="/">Home</a><br>
		<h2>arf DNS domain and Internet IP Address</h2>
		<span class="text"><b>Current domain:</b> arf20.com</span><br>
		<span class="text"><b>Public gateway IP address:</b> <?php printIPAddr(); ?></span>
		<h2>Available services of the arf network</h2>
		<table class="sang">
		
			<tr><th>Service name</th><th>Application</th><th>Application protocol</th><th>Transport protocol</th><th>IP port</th><th>Host</th><th><a href="?status=true">Status</a></th></tr>
			
			<tr><td>Web Server</td><td>nginx 1.18.0</td><td>HTTP</td><td>TCP</td><td>80</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(80); $progress = 1; update(); } ?></tr>
			
			<tr><td>Web Server</td><td>nginx 1.18.0</td><td>HTTPS</td><td>TCP</td><td>443</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(443); $progress++; update(); } ?></tr>
			
			<tr><td>RTMP Streaming Ingest</td><td>nginx 1.18.0</td><td>RTMP</td><td>TCP</td><td>1935</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(1935); $progress++; update(); } ?></tr>
			
			<tr><td>SSH/SFTP Server (private)</td><td>Bitvise SSH Server</td><td>SSH/SFTP</td><td>TCP</td><td>22</td><td>DESKTOP (hardened decoy for bots)</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(22); $progress++; update(); } ?></tr>
			
			<tr><td>RDP Server (private)</td><td>Windows RDP</td><td>RDP</td><td>TCP</td><td>3389</td><td>DESKTOP</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(3389); $progress++; update(); } ?></tr>
			
			<tr><td>SSH/SFTP Server (private)</td><td>OpenSSH</td><td>SSH/SFTP</td><td>TCP</td><td>24</td><td>raspi</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(24); $progress++; update(); } ?></tr>
			
			<tr><td>FTP Server (private)</td><td>vsftpd</td><td>FTP</td><td>TCP</td><td>21</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(21); $progress++; update(); } ?></tr>
			
			<tr><td>PPTP VPN Server (private)</td><td>Poptop pptpd</td><td>PPTP</td><td>TCP</td><td>1723</td><td>raspi (soon server)</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(1723); $progress++; update(); } ?></tr>
			
			<tr><td>Speed Testing Server</td><td>iperf3</td><td>iperf3</td><td>TCP</td><td>5201</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(5201); $progress++; update(); } ?></tr>
			
			<tr><td>Minecraft Server A (minepau)</td><td>Java Minecraft Server 1.16</td><td>Minecraft idk</td><td>TCP</td><td>25565</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(25565); $progress++; update(); } ?></tr>
			
			<tr><td>Minecraft Server B (shupapingones)</td><td>Java Minecraft Server 1.16</td><td>Minecraft idk</td><td>TCP</td><td>25566</td><td>DESKTOP</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(25566); $progress++; update(); } ?></tr>
			
			<tr><td>RTL-TCP Server</td><td>rtl_tcp</td><td>whatever rtl_tcp uses</td><td>TCP</td><td>1234</td><td>DESKTOP</td>
				<?php if ($_GET["status"] == "true") { update(); printStatusPort(1234); $progress++; update(); } ?></tr>
				
			<tr><td>NTP Server</td><td>ntpd</td><td>NTP</td><td>UDP</td><td>123</td><td>server</td>
				<?php if ($_GET["status"] == "true") { update(); $progress++; update(); } ?></tr>
			
		</table>
		
		<h2>ARFNET Hosts</h2>
		<table class="sang">
			<tr><th>Host</th><th>IP</th><th>Status</th></tr>
			<tr><td>server</td><td><?php echo $subnet."4"; ?></td><?php if ($_GET["status"] == "true") { update(); printHostStatus($subnet."4"); $progress++; update(); } ?></tr>
			<tr><td>desktop</td><td><?php echo $subnet."8"; ?></td><?php if ($_GET["status"] == "true") { update(); printHostStatus($subnet."8"); $progress++; update(); } ?></tr>
			<tr><td>raspi</td><td><?php echo $subnet."14"; ?></td><?php if ($_GET["status"] == "true") { update(); printHostStatus($subnet."14"); $progress++; update(); } ?></tr>
		</table>
		
		<h2>ARFNET ISP backbone</h2>
		<span class="text"><b>Current ISP:</b> vodafone (ono)</span><br>
		<span class="text"><b>Type of backbone:</b> cable</span><br>
		<span class="text"><b>Speed:</b> 600mbps downlink, 100mbps uplink</span><br>
		<span class="text"><b>Router:</b> ISP provided, cablemodem + router + wap</span><br>
    </body>
</html>