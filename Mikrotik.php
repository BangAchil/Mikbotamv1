<?php
//=====================================================PLEASE NOT TO BE DELETED

/*
 *
 *  moded    	: BangAchil
 *  Email     	: kesumaerlangga@gmail.com
 *  Telegram  	: @bangachil
 *
 *  Name      	: Mikrotik bot telegram - php
 *  Fungsi    	: Monitoring mikortik api (Edit Rule Comingsoon )
 *  Pembuatan 	: November 2018
 *  version     :  3.5.0   last 1.0.0, 1.2.0, 1.3.0  
 *  Thanksto  :  *SengkuniCode Devlop
                 *@Alnyz
                 *@shinauleebeenvinter
                 *Other yang udah bantu pembuatan project ini
*/

//=====================================================PLEASE NOT TO BE DELETED

 //Apa yang baru
 //PPP Menu
 //!user cari user
 //Hapus module file get content
 //live update
 //Lulus uji
 //Lulus syntax chek
 //edit file di dalam folder config lalu edit file config.php




 //Yang baru download silahkan ikuti langkah langkah berikut

 /************************************************************************************
 * ** methode long poolling** *
 * Perisapkan Sebuah PC atau sebuah vps
 * OS windows Linux other
 * Internet
 * InstalL Apliaksi WEBSERVER (OS WINSOWS XAMPP, APPSERV )
 * Copy file zip ini didalam sebuah folder root www/htdocs ()
 * extrack file
 * edit iprouter username dan pasword token
 * Kemudian simpan
 * edit file di dalam folder config lalu edit file config.php
 * edit token bot dan username bot
 * Kemudian simpan
 * Anda bisa langsung menjalankan bot
 * dengan cara menggunakan CMD bukan membukanya melalui webbrowser
 * Langkah - Langkah Running bot
 *   * Masuk ke tempat file mikbotam berada
 *   * tekan CTRL + klik kanan maouse
 *   * Kemudian sort cousor ke Open command window here
 *   * Muncul window CMD
 *   * Run bot dengan Mengetikan php mikrotik.php Kemudian Enter
 *   * Jika anda melihat sebuah text
 *             FrameBot version 1.5
 *             Mode    : Long Polling
 *                 Debug   : ON
 *   * Selamat Bot anda berjalan
 * jika error pastikan komputer terhubung ke internet dan dapat melakukan ping ke mikrotik
 * Edit file mikrotik.php sesuai Kebutuhan anda happy coding
 *
 *****************************************************************************/

 /*
  * ** methode webhook hosting ** *
 *Comingsoon
 */

 //Bedanya longpoling dan webhook bisa cari digoogle ya
 //bot ini sama persis dengan mikbotam silahkan diedit sesuai keinginan anda

date_default_timezone_set('Asia/Jakarta');

//config mikrotik
//config sekarang ada di folder config :D
//edit file yang berada di folder config.php

include('./config/config.php');
//include
require_once 'src/FrameBot.php';
$bot = new FrameBot($datasa['token'], $datasa['usernamebot']);
require_once ('./include/formatbytesbites.php');
require_once ('./include/routeros_api.class.php');
//fungsi

//=====================================================================================================
$bot->cmd('/start|/Start', function () {
	$text = "Mikrotik bot telegram see command in /help";
	$options = ['parse_mode' => 'html'];
	return Bot::sendMessage($text, $options);
});
$bot->cmd('/traffic|/Traffic|traffic|!Traffic', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	$texta = "👨‍💻 Mohon ditunggu\nPermintaan sedang diprosses";
	Bot::sendMessage($texta);
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$getinterface = $API->comm("/interface/print");
		$num = count($getinterface);
		for ($i = 0;$i < $num;$i++) {
			$interface = $getinterface[$i]['name'];
			$getinterfacetraffic = $API->comm("/interface/monitor-traffic", array("interface" => "$interface", "once" => "",));
			$tx = formatBites($getinterfacetraffic[0]['tx-bits-per-second'], 1);
			$rx = formatBites($getinterfacetraffic[0]['rx-bits-per-second'], 1);
			$Traffic.= "Traffic $interface\n";
			$Traffic.= "====================\n";
			$Traffic.= "TX: <code>$tx / 100 Mbps </code>\n";
			$Traffic.= "RX: <code>$rx / 100 Mbps </code>\n";
			$Traffic.= "====================\n";
		}
	}
	$arr2 = str_split($Traffic, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$texta = $arr2[$i];
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
});
$bot->cmd('/dhcp|/Dhcp|!dhcp|!Dhcp', function ($dhcp) {
		global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($dhcp == 'Lease') {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$get_lease = $API->comm("/ip/dhcp-server/lease/print");
			$num = count($get_lease);
			$data.= "<b>DHCP Lease $num </b>\n\n";
			for ($i = 0;$i < $num;$i++) {
				$lease = $get_lease[$i];
				$id = $lease['.id'];
				$address = $lease['address'];
				$macaddress = $lease['mac-address'];
				$server = $lease['server'];
				$acaddr = $lease['active-address'];
				$acmac = $lease['active-mac-address'];
				$hostname = $lease['host-name'];
				$host = str_replace("android", "AD", $hostname);
				$status = $lease['status'];
				if ($lease['dynamic'] == "true") {
					$dy = "🎯 Dynamic";
				} else {
					$dy = "📝 Static";
				}
				$data.= "🔎 Dhcp to $address \n  ";
				$data.= "┠  <code>$dy</code>  \n";
				$data.= "  ┠ <code>IP   : $address</code>\n";
				$data.= "  ┠ <code>Mac  : $macaddress</code>\n";
				$data.= "  ┠ <code>DHCP : $server</code>\n";
				$data.= "  ┗ <code>HOST : $host</code>\n\n";
			}
		} else {
			$dataaa = "Tidak Terkoneksi Dengan Mikrotik Coba Lagi";
			Bot::sendMessage($dataaa);
		}
	} else if ($dhcp == 'Server') {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/ip/dhcp-server/print");
			$num = count($ARRAY);
			$data.= "<b>        DHCP Server $num </b>\n\n";
			for ($i = 0;$i < $num;$i++) {
				$name = $ARRAY[$i]['name'];
				$interface = $ARRAY[$i]['interface'];
				$lease = $ARRAY[$i]['lease-time'];
				$bootp = $ARRAY[$i]['bootp-support'];
				$authoritative = $ARRAY[$i]['authoritative'];
				$use_radius = $ARRAY[$i]['use-radius'];
				$dynamic = $ARRAY[$i]['dynamic'];
				$disable = $ARRAY[$i]['disabled'];
				$no = $i+1;
				$data.= "\n📋 Dhcp Server $no\n";
				$data.= "┠ <code>Nama          :$name</code>\n";
				$data.= "┠ <code>Interface     :$interface</code> \n";
				$data.= "┠ <code>Lease-time    :$lease</code> \n";
				$data.= "┠ <code>Bootp-support :$bootp</code> \n";
				$data.= "┠ <code>Authoritative :$authoritative</code>\n";
				$data.= "┠ <code>Use-radius    :$use_radius</code>\n";
				if ($dynamic == "true") {
					$data.= "┠ <code>Dynamic       : Iya </code>\n";
				} else {
					$data.= "┠ <code>Dynamic       : Tidak </code>\n";
				}
				if ($disable == "true") {
					$data.= "┗ <code>Status        : ⚠ Disable</code>\n";
				} else {
					$data.= "┗ <code>Status        : ✔ Enable </code>\n";
				}
			}
		} else {
			$dataa = "Tidak Terkoneksi Dengan Mikrotik Coba Lagi";
			Bot::sendMessage($dataa);
		}
	} else {
		$text = "Dhcp Server or Lease\n";
		$keyboard = [['!Dhcp Server', '!Dhcp Lease'], ['Help','!Hide'],];
		$replyMarkup = ['reply_to_message_id' => $msgid,'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => false];
		$anu['reply_markup'] = json_encode($replyMarkup);
		Bot::sendMessage($text, $anu);
	}
	$arr2 = str_split($data, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$texta = $arr2[$i];
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
});
$bot->cmd('/Dns|/dns|!dns|!Dns', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username   = $datasa['user'];
	$mikrotik_password   = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm("/ip/dns/print");
		$Ipserver = $ARRAY[0]['servers'];
		$dyserver = $ARRAY[0]['dynamic-servers'];
		$Allow = $ARRAY[0]['allow-remote-requests'];
		$cache = $ARRAY[0]['cache-used'];
		$text.= "🌏 DNS\n";
		$text.= "┠ Server :$Ipserver\n";
		$text.= "┠ Dynamic Server :$dyserver\n";
		if ($Allow == "true") {
			$text.= "┠ Allow Remote : Iya \n";
		} else {
			$text.= "┠ Allow Remote : Tidak \n";
		}
		$text.= "┗ Cache Used  :$cache \n";
	} else {
		$text = "Tidak dapat terhubung dengan mikrotik coba kembali";
	}
			$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($text, $options);
});
$bot->cmd('/Ping|/ping|ping|PING|Ping|!Ping', function ($address) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($address == NULL) {
		$text .= "\nPing latency\n=======================\n";
		$text.= "Contoh Penggunaan :\n";
		$text.= "=======================\n";
		$text.= "ping google.com\n";
		$text.= "ping detik.com\n";
		$text.= "ping kompas.com\n";
		$text.= "ping youtube.com\n";
		Bot::sendMessage($text);
	} else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $address)) {
		$texta = "Mohon Ditunggu Permintaan Sedang Diproses";
		Bot::sendMessage($texta);
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$PING = $API->comm("/ping", array("address" => "$address", "count" => "5",));
			$num = count($PING);
			$text = "<b>Ping  $address</b>\n\n";
			for ($i = 0;$i < $num;$i++) {
				$hot = $PING[$i]['host'];
				$status = $PING[$i]['status'];
				$size = $PING[$i]['size'];
				$ttl = $PING[$i]['ttl'];
				$time = $PING[$i]['time'];
				$packet_loss = $PING[$i]['packet-loss'];
				$avg = $PING[$i]['avg-rtt'];
				$packet_loss = $PING[$i]['packet-loss'];
				if ($status == 'timeout') {
					$text.= "<code>PING $hot \nStatus $status Loss $packet_loss% </code>\n\n";
				} else {
					$text.= "<code>PING $hot \nSize $size TTL $ttl \nTime $time AVG $avg</code>\n\n";
				}
			}
		} else {
			$text = "Tidak Terkoneksi Dengan Mikrotik Coba Lagi";
		}
				$options = ['parse_mode' => 'html'];
		Bot::sendMessage($text, $options);
	
	} elseif (preg_match('/^([a-zA-Z0-9]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9])?\.)?([a-zA-Z0-9]{1,2}([-a-zA-Z0-9]{0,252}[a-zA-Z0-9])?)\.([a-zA-Z]{2,63})$/', strtolower($address))) {
		$texta = "Mohon Ditunggu Permintaan Sedang Diproses";
		Bot::sendMessage($texta);
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$PING = $API->comm("/ping", array("address" => "$address", "count" => "5",));
			$num = count($PING);
			$text = "<b>Ping  $address</b>\n\n";
			for ($i = 0;$i < $num;$i++) {
				$hot = $PING[$i]['host'];
				$status = $PING[$i]['status'];
				$size = $PING[$i]['size'];
				$ttl = $PING[$i]['ttl'];
				$time = $PING[$i]['time'];
				$packet_loss = $PING[$i]['packet-loss'];
				$avg = $PING[$i]['avg-rtt'];
				$packet_loss = $PING[$i]['packet-loss'];
				if ($status == 'timeout') {
					$text.= "<code>PING $hot \nStatus $status Loss $packet_loss% </code>\n\n";
				} else {
					$text.= "<code>PING $hot \nSize $size TTL $ttl \nTime $time AVG $avg</code>\n\n";
				}
			}
		} else {
			$text = "Tidak Terkoneksi Dengan Mikrotik Coba Lagi";
		}
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($text, $options);
	}
});
$bot->cmd('/monitor|monitor|!Monitor|/Monitor', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	$info = bot::message();
	$id = $info['chat']['id'];
	$iduser = $info['from']['id'];
	$msgid = $info['message_id'];
	$textaa = "👨‍💻 Mohon ditunggu\nPermintaan sedang diprosses";
	Bot::sendMessage($textaa);
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)){
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.7",  //ubah sesuai dengan ip yang akan di monitor
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 1 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%"; //ubah text
		} else {
			$data = "👨‍💻PING WIFI 1 ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%"; //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.6", //ubah sesuai dengan ip yang akan di monitor
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 2 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%";  //ubah text
		} else {
			$data = "👨‍💻PING WIFI 2  ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";   //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.1",  //ubah sesuai dengan ip yang akan di monitor
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 3 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%";  //ubah text
		} else {
			$data = "👨‍💻PING WIFI 3  ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";   //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.2", 
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 4 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%";  //ubah text
		} else {
			$data = "👨‍💻PING WIFI 4 ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";   //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.3",  //ubah sesuai dengan ip yang akan di monitor
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 5 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%";  //ubah text
		} else {
			$data = "👨‍💻PING WIFI 5 ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";   //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.9", 
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 6 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%";  //ubah text
		} else {
			$data = "👨‍💻PING WIFI 6 ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";  //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
		$PING = $API->comm("/ping", array(
			"address" => "10.150.1.8",  //ubah sesuai dengan ip yang akan di monitor
			"count" => "1",
			));
		$hot = $PING[0]['host'];
		$status = $PING[0]['status'];
		$size = $PING[0]['size'];
		$ttl = $PING[0]['ttl'];
		$time = $PING[0]['time'];
		$packet_loss = $PING[0]['packet-loss'];
		$avg = $PING[0]['avg-rtt'];
		if ($status == 'timeout') {
			$data = "👨‍💻PING WIFI 7 ⚠ Down \nHost :$hot Status : <b>$status</b> Loss :$packet_loss%"; //ubah text
		} else {
			$data = "👨‍💻PING WIFI 7 ✔ Reply \nHost :$hot Time : <b>$time</b> Loss :$packet_loss%";  //ubah text
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($data, $options);
	} else {
		$textA = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
		Bot::sendMessage($textA, $options);
	}
	return Bot::sendMessage($text, $options);
});
$bot->cmd('/interface|/Interface|!interface', function ($bridge) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($bridge == 'bridge') {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm('/interface/bridge/print');
			// kumpulkan data
			$num = count($ARRAY);
			for ($i = 0;$i < $num;$i++) {
				$nama = $ARRAY[$i]['name'];
				$mtu = $ARRAY[$i]['mtu'];
				$Mac_status = $ARRAY[$i]['mac-address'];
				$pro = $ARRAY[$i]['protocol-mode'];
				$run = $ARRAY[$i]['running'];
				$Disable = $ARRAY[$i]['disabled'];
				$text.= "\n";
				$text.= "🚗 Bridge\n";
				$text.= "┠ Nama : $nama\n";
				$text.= "┠ Mtu : $mtu \n";
				$text.= "┠ Mac : $Mac_status \n";
				$text.= "┠ Protocol : $pro \n";
				if ($run == "true") {
					$text.= "┠ Active : Iya \n";
				} else {
					$text.= "┠ Active : Tidak \n";
				}
				if ($Disable == "false") {
					$text.= "┠ Disable : Tidak \n";
				} else {
					$text.= "┠ Disable : Iya \n";
				}
				$text.= "┠ Disablenow  : hidden  \n";
			}
			$text.= "┗ Enablenow : hidden \n";
		} else {
			$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali ";
		}
	} else if ($bridge == 'List') {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/interface/print");
			$num = count($ARRAY);
			for ($i = 0;$i < $num;$i++) {
				$no = $i + 1;
				$ids = $ARRAY[$i]['.id'];
				$dataid = str_replace('*', 'id', $ids);
				$namaport = $ARRAY[$i]['name'];
				$comentport = $ARRAY[$i]['comment'];
				$typeport = $ARRAY[$i]['type'];
				$tx = formatBytes($ARRAY[$i]['rx-byte']);
				$rx = formatBytes($ARRAY[$i]['rx-byte']);
				$true = $ARRAY[$i]['running'];
				$text.= " \n ";
				$text.= "💻 Interface$no \n ";
				if ($true == "true") {
					$text.= " ┠🆙 CONNECT \n";
				} else {
					$text.= " ┠⚠ DISCONNECT\n";
				}
				$text.= "  ┠ Nama : $namaport \n";
				$text.= "  ┠ Comment : $comentport  \n";
				$text.= "  ┠ Type : $typeport \n";
				$text.= "  ┠ Download : $tx\n";
				$text.= "  ┠ Upload : $rx\n";
				$text.= "  ┠ Disablenow  :/InDlE$dataid  \n";
				$text.= "  ┗ Enablenow :/InEle$dataid \n";
			}
		} else {
			$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
		}
	} else {
		$texta = "List or Bridge";
		$keyboard = [['!interface List', '!interface bridge'],['!Menu', '!Help'], ['!Address', '!Hide'],];
		$replyMarkup = ['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true,];
		$options = [
			'reply' => true,
			'reply_markup' => json_encode($replyMarkup),
		];
		Bot::sendMessage($texta, $options);
	}
		$arr2 = str_split($text, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$texta = $arr2[$i];
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
});
$bot->cmd('/Pool|/pool', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm("/ip/pool/print");
		// kumpulkan data
		$num = count($ARRAY);
		for ($i = 0;$i < $num;$i++) {
			$namapool = $ARRAY[$i]['name'];
			$rannge = $ARRAY[$i]['ranges'];
			$id = $ARRAY[$i]['.id'];
			$text.= "🎯 \n";
			$text.= "┠ Nama :$namapool\n";
			$text.= "┠ range:$rannge\n";
			$text.= "┗ ID   :$id \n";
		}
	} else {
		$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
	}
	return Bot::sendMessage($text);
});
$bot->cmd('/address|/Address|!Address', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm("/ip/address/print");
		$num = count($ARRAY);
		$text.= "Daftar IP Address $num\n";
		for ($i = 0;$i < $num;$i++) {
			$address = $ARRAY[$i]['address'];
			$network = $ARRAY[$i]['network'];
			$interface = $ARRAY[$i]['interface'];
			$dynamic = $ARRAY[$i]['dynamic'];
			$disabled = $ARRAY[$i]['disabled'];
			// ambil data
			$text.= "\n♨  $interface\n";
			$text.= "┠ IP address : $address\n";
			$text.= "┠ Network    : $network \n";
			$text.= "┠ interface  : $interface \n";
			// CARI kata true
			if ($dynamic == "true") {
				$text.= "┠ Dynamic : Iya \n";
			} else {
				$text.= "┠ Dynamic : Tidak \n";
			}
			// pecah kata false
			if ($disabled == "false") {
				$text.= "┠ Disable : Tidak  \n";
			} else {
				$text.= "┠ Disable : Yes  \n";
			}
			$text.= "┠ Disablenow  : hidden  \n";
			$text.= "┗ Enablenow : hidden \n";
		}
	}
			$arr2 = str_split($text, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$texta = $arr2[$i];
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
});
$bot->cmd('!Hotspot|/Hotspot|/hotspot', function ($user) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		if ($user == 'aktif') {
			$gethotspotactive = $API->comm("/ip/hotspot/active/print");
			$TotalReg = count($gethotspotactive);
			$text.= "User Aktif $TotalReg item\n\n";
			for ($i = 0;$i < $TotalReg;$i++) {
				$hotspotactive = $gethotspotactive[$i];
				$id = $hotspotactive['.id'];
				$server = $hotspotactive['server'];
				$user = $hotspotactive['user'];
				$address = $hotspotactive['address'];
				$mac = $hotspotactive['mac-address'];
				$uptime = $hotspotactive['uptime'];
				$usesstime = $hotspotactive['session-time-left'];
				$bytesi = formatBytes($hotspotactive['bytes-in'], 2);
				$byteso = formatBytes($hotspotactive['bytes-out'], 2);
				$loginby = $hotspotactive['login-by'];
				$comment = $hotspotactive['comment'];
				$text.= "👤 User aktif\n";
				$text.= "┠ ID :$id\n";
				$text.= "┠ Server :$server\n";
				$text.= "┠ User :$user\n";
				$text.= "┠ IP :$address\n";
				$text.= "┠ Uptime:$uptime\n";
				$text.= "┠ B IN :$bytesi\n";
				$text.= "┠ B OUT :$byteso\n";
				$text.= "┠ Sesion:$usesstime\n";
				$text.= "┗ Login :$loginby\n \n";
			}
			$arr2 = str_split($text, 4000);
			$amount_gen = count($arr2);
			for ($i = 0;$i < $amount_gen;$i++) {
				$texta = $arr2[$i];
				$options = [
					'reply' => true,
				];
				Bot::sendMessage($texta, $options);
			}
		} elseif ($user == 'user') {
			$ARRAY = $API->comm("/ip/hotspot/user/print");
			$num = count($ARRAY);
			$text = "Total $num User\n\n";
			for ($i = 0;$i < $num;$i++) {
				$no = $i+1;
				$data = $ARRAY[$i]['.id'];
				$dataid = str_replace('*', 'id', $data);
				$server = $ARRAY[$i]['server'];
				$name = $ARRAY[$i]['name'];
				$password = $ARRAY[$i]['password'];
				$mac = $ARRAY[$i]['mac-address'];
				$profile = $ARRAY[$i]['profile'];
				$limit = $ARRAY[$i]['limit-uptime'];
				$text.= "👥 $no  ($dataid)\n";
				$text.= "┣ Server :$server \n";
				$text.= "┣ Nama : $name\n";
				$text.= "┣ password : $password \n";
				$text.= "┣ mac : $mac\n";
				$text.= "┣ Profil : $profile\n";
				$text.= "┣ limit : $limit\n";
				$text.= "┗ RemoveNow User /rEm0v$dataid\n\n";
			}
			$arr2 = str_split($text, 4000);
			$amount_gen = count($arr2);
			for ($i = 0;$i < $amount_gen;$i++) {
				$texta = $arr2[$i];
				$options = [
					'reply' => true,
				];
				Bot::sendMessage($texta, $options);
			}
		} else {
			$text = "User list Or aktif";
			$keyboard = [['!Hotspot user', '!Hotspot aktif'], ['!Menu', '!Help'], ['!Hide'],];
			$replyMarkup = ['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true];
			$options = [
				'reply' => true,
				'reply_markup' => json_encode($replyMarkup),
			];
			Bot::sendMessage($text, $options);
		}
	} else {
		$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
		$options = [
			'reply' => true,
		];
		Bot::sendMessage($text, $options);
	}
});
$bot->cmd('!Generate', function ($limit_download, $limit_upload, $profile) {
	$textA = "Comingsoon";
	Bot::sendMessage($textA);
});
$bot->cmd('/neighbor|/Neighbor', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY3 = $API->comm("/ip/hotspot/user/print");
		$ARRAY2 = $API->comm("/system/scheduler/print");
		$ARRAY = $API->comm("/ip/neighbor/print");
		$num = count($ARRAY);
		$num2 = count($ARRAY2);
		$num3 = count($ARRAY3);
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			$interfaces = "<code>" . $ARRAY[$i]['interface'] . "</code>";
			$identity = "<code>" . $ARRAY[$i]['identity'] . "</code>";
			$address = "<code>" . $ARRAY[$i]['address'] . "</code>";
			$mac = "<code>" . $ARRAY[$i]['mac-address'] . "</code>";
			$version = "<code>" . $ARRAY[$i]['version'] . "</code>";
			$uptime = "<code>" . $ARRAY[$i]['uptime'] . "</code>";
			$text.= "👥  $no\n";
			$text.= "┣ Interface :  $interfaces \n";
			$text.= "┣ Nama : $identity\n";
			$text.= "┣ IP address : $address \n";
			$text.= "┣ Mac : $mac\n";
			$text.= "┣ version :    $version\n";
			$text.= "┗ Uptime :     $uptime\n\n";
		}
	} else {
		$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
	}
			$arr2 = str_split($text, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$texta = $arr2[$i];
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
});
$bot->cmd('/resource|/Resource|!Resource', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$health = $API->comm("/system/health/print");
		$dhealth = $health['0'];
		$ARRAY = $API->comm("/system/resource/print");
		$first = $ARRAY['0'];
		$memperc = ($first['free-memory'] / $first['total-memory']);
		$hddperc = ($first['free-hdd-space'] / $first['total-hdd-space']);
		$mem = ($memperc * 100);
		$hdd = ($hddperc * 100);
		$sehat = $dhealth['temperature'];
		$platform = $first['platform'];
		$board = $first['board-name'];
		$version = $first['version'];
		$architecture = $first['architecture-name'];
		$cpu = $first['cpu'];
		$cpuload = $first['cpu-load'];
		$uptime = $first['uptime'];
		$cpufreq = $first['cpu-frequency'];
		$cpucount = $first['cpu-count'];
		$memory = formatBytes($first['total-memory']);
		$fremem = formatBytes($first['free-memory']);
		$mempersen = number_format($mem, 3);
		$hdd = formatBytes($first['total-hdd-space']);
		$frehdd = formatBytes($first['free-hdd-space']);
		$hddpersen = number_format($hdd, 3);
		$sector = $first['write-sect-total'];
		$setelahreboot = $first['write-sect-since-reboot'];
		$kerusakan = $first['bad-blocks'];
		$text.= "<b>📡 Resource</b>\n";
		$text.= "<code>Boardname: $board</code>\n";
		$text.= "<code>Platform : $platform</code>\n";
		$text.= "<code>Uptime is: $uptime</code>\n";
		$text.= "<code>Cpu Load : $cpuload%</code>\n";
		$text.= "<code>Cpu type : $cpu</code>\n";
		$text.= "<code>Cpu Hz   : $cpufreq Mhz/$cpucount core</code>\n==========================\n";
		$text.= "<code>Free memory and memory \n$memory-$fremem/$mempersen %</code>\n==========================\n";
		$text.= "<code>Free disk and disk      \n$hdd-$frehdd/$hddpersen %</code>\n==========================\n";
		$text.= "<code>Since reboot, bad blocks \n$sector-$setelahreboot/$kerusakan%</code>\n==========================\n";
	}
	$options = ['reply' => true,'parse_mode' => 'html',];
	return Bot::sendMessage($text, $options);
});
$bot->cmd('/ipbinding|/Ipbinding', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm('/ip/hotspot/ip-binding/getall');
		$num = count($ARRAY);
		$baris = $ARRAY;
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			$id = "<code>" . $baris[$i]['.id'] . "</code>";
			$address = "<code>" . $baris[$i]['address'] . "</code>";
			$mac = "<code>" . $baris[$i]['mac-address'] . "</code>";
			$toaddress = "<code>" . $baris[$i]['to-address'] . "</code>";
			$server = "<code>" . $baris[$i]['server'] . "</code>";
			$type = "<code>" . $baris[$i]['type'] . "</code>";
			$comment = "<code>" . $baris[$i]['comment'] . "</code>";
			$disabled = "<code>" . $baris[$i]['disabled'] . "</code>";
			$text.= "👥  $no\n";
			$text.= "┣Address :  $address \n";
			$text.= "┣Mac address :  $mac \n";
			$text.= "┣To address  : $toaddress\n";
			$text.= "┣Server      : $server \n";
			$text.= "┣Type    : $type\n";
			$text.= "┗Disable : $disabled\n\n";
		}
	} else {
		$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
	}
	$options = ['reply' => true,'parse_mode' => 'html',];
	return Bot::sendMessage($text, $options);
});
$bot->cmd('+user|!adduser', function ($server, $username, $password, $limit_uptime) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if (empty($server) || empty($username) || empty($username) || empty($limit_uptime)) {
		$texts .= "Format salah masukan secara berurutan\n";
		$texts .= "+user (profile) (user) (password) (limit-uptime)\n Contoh  :\n\n+user siswa al al 1h\n+user guru bambang bambang 1d";
		Bot::sendMessage($texts);
	} else {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$add_user_api = $API->comm("/ip/hotspot/user/add", array(
				"profile" => $server,
				"name" => $username,
				"password" => $password,
				"limit-uptime" => $limit_uptime,
			));
			$textaa = json_encode($add_user_api);
			if (strpos(strtolower($textaa), 'failure: already have user with this name for this server') !== false) {
				$gagal = $add_user_api['!trap'][0]['message'];
				$texta.= "⛔ Gagal Menginput user baru pastikan mengisikannya dengan benar \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($textaa), 'ambiguous value of profile, more than one possible value matches input') !== false) {
				$gagal = $add_user_api['!trap'][0]['message'];
				$texta.= "⛔ Gagal Menginput user baru pastikan mengisikannya dengan benar \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($textaa), 'invalid time value for argument limit-uptime') !== false) {
				$gagal = $add_user_api['!trap'][0]['message'];
				$texta.= "⛔ Gagal Menginput user baru pastikan mengisikannya dengan benar \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($textaa), 'input does not match any value of profile') !== false) {
				$gagal = $add_user_api['!trap'][0]['message'];
				$texta.= "⛔ Gagal Menginput Profile Tidak ditemukan \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($textaa), 'input does not match any value of profile') !== false) {
				$gagal = $add_user_api['!trap'][0]['message'];
				$texta.= "⛔ Gagal Menginput Profile Tidak ditemukan \n\n<b>KETERANGAN   :</b>\n$gagal";
			} else {
				$texts.= "User Berhasil dibuat \n\n";
				$dataid = str_replace('*', 'id', $add_user_api);
				$texts.= "RemoveNow   : /rEm0v$dataid\n";
				$options = ['reply' => true,'parse_mode' => 'html',];
	        return Bot::sendMessage($texts, $options);
				$text.= "<code>=========================</code>\n\n";
				$text.= "<b>                 CustomText</b>\n";
				$text.= "<b>       Costumtext</b>\n\n";
				$text.= "<code>=========================</code>\n";
				$text.= "<code>  ID         : $add_user_api</code>\n";
				$dataida = str_replace('h', ' Jam', $limit_uptime);
				$text.= "<code>  Expe       : $dataida</code>\n";
				$text.= "<code>  Name       : $username</code>\n";
				$text.= "<code>  Password   : $password</code>\n";
				$text.= "<code>=========================</code>\n";
				$text.= "<code>GUNAKAN INTERNET DGN BIJAK </code>\n";
				$text.= "<code>=========================</code>\n";
				$dnsname 		= 'wifi.masukdesa';
				$nameprodak 	= urlencode('ICORE PLUS NETWORKS');
				$chl = urlencode("http://$dnsname/login?username=$username&password=$password");
				$qrcode = 'http://qrickit.com/api/qr.php?d='.$chl.'&addtext='.$nameprodak.'&txtcolor=000000&fgdcolor=E90716&bgdcolor=FFFFFF&qrsize=500';
				$options = [
					'caption' => $text,
					'parse_mode' => 'html',
				];
				sleep(3);
				Bot::sendPhoto($qrcode, $options);
			}
		} else {
			$texta = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
		}
		$options = ['reply' => true,'parse_mode' => 'html',];
		return Bot::sendMessage($texta, $options);
	}
});
$bot->cmd('/qrcode', function () {
	$info = bot::message();
	$json = file_get_contents("./include/data.json");
	$datas = json_decode($json, TRUE);
	$token = $datas['token'];
	$ambilgambar = $info['reply_to_message']['photo'][2]['file_id'];
	if (empty($ambilgambar)) {
		$text = "Balas Gambar/foto QRcode";
		Bot::sendMessage($text);
	} else {
		$cek = Bot::getFile($ambilgambar);
		$hasilkirimaaa = json_decode($cek,true);
		$hasilurl = $hasilkirimaaa['result']['file_path'];
		$urlkirim = 'http://api.qrserver.com/v1/read-qr-code/?fileurl=https://api.telegram.org/file/bot'.$token.'/'.$hasilurl;
		$hasilurla = file_get_contents($urlkirim);
		$hasilkirim = json_decode($hasilurla,true);
		$terjemah = $hasilkirim[0]['symbol'][0]['data'];
		return Bot::sendMessage($terjemah);
	}
});
$bot->cmd('!user|!User', function ($ids) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
			if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
				$ARRAY = $API->comm("/ip/hotspot/user/print", array("?name" => "$ids",));
				
				if (empty($ARRAY)) {
					$texta = "User tidak ditemukan,";
				} else {
					foreach ($ARRAY as $index => $baris) {
						$hasil7.="Server  :" .$baris['server'];
						$text.= "Nama     :" . $baris['name'] . "\n";
						$text.= "Password :" . $baris['password'] . "\n";
						$text.= "Limit    :" . $baris['limit-uptime'] . "\n";
						$text.= "Uptime   :" . $baris['uptime'] . "\n";
						$text.= "Upload   :" . formatBytes( $baris['limit-bytes-in']) . "\n";
						$text.= "Downlaod :" . formatBytes($baris['limit-bytes-out']) . "\n";
						$text.= "Profil   :" . $baris['profile'] . "\n=======================\n";
						$data = $baris['.id'];
						$dataid = str_replace('*', 'id', $data);
					}
					$texta = "<b>" . $hasil7 . "</b>\n<code>" . $text . "</code>\nRemove User /rEm0v$dataid\n\n";
				}


			}
		
		$arr2 = str_split($texta, 4000);
		$amount_gen = count($arr2);
		for ($i = 0;$i < $amount_gen;$i++) {

			$textaa = $arr2[$i];
			$options = ['parse_mode' => 'html',];
			Bot::sendMessage($textaa, $options);

		}

	
});
$bot->cmd('/userbyprofile|/Userbyprofile', function ($ids) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if (empty($ids)) {
		$texta = "Masukan profile Setelah Perintah\nContoh : /Userbyprofile admin\n";
		Bot::sendMessage($texta);
	} else {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/ip/hotspot/user/print", array("?profile" => "$ids",));
			if (empty($ARRAY)) {
				$texta = "TIDAK DITEMUKAN";
			} else {
				foreach ($ARRAY as $index => $barisan) {
					$hasil7 = $barisan['profile'];
					$text.= "Nama     :" . $barisan['name'] . "\n";
					$text.= "Password :" . $barisan['password'] . "\n";
					$text.= "Limit    :" . $barisan['limit-uptime'] . "\n";
					$text.= "Uptime   :" . $barisan['uptime'] . "\n";
					$text.= "Upload   :" . $barisan['bytes-in'] . "\n";
					$text.= "Downlaod :" . $barisan['bytes-out'] . "\n";
					$text.= "Profil   :" . $barisan['profile'] . "\n=======================\n";
				}
				$texta = "<b>" . $hasil7 . "</b>\n<code>" . $text . "</code>";
				$arr2 = str_split($texta, 4000);
				$amount_gen = count($arr2);
				for ($i = 0;$i < $amount_gen;$i++) {
					$textaa = $arr2[$i];
					$options = ['reply' => true,'parse_mode' => 'html',];
					Bot::sendMessage($texta, $options);
				}
			}
		} else {
			$text = "Tidak dapat Terhubung dengan Mikrotik Coba Kembali";
			$options = ['reply' => true,'parse_mode' => 'html',];
			return Bot::sendMessage($text, $options);
		}
	}
});
$bot->cmd('/userprofile|/Userprofile', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm('/ip/hotspot/user/profile/print');
		$data = $ARRAY;
		$text.= "=======================\n";
		foreach ($data as $index => $baris) {
			$text.= "ID           :" . $baris['.id'] . "\n";
			$text.= "Name         :" . $baris['name'] . "\n";
			$text.= "Shared User  :" . $baris['shared-users'] . "\n";
			$text.= "Add Mac      :" . $baris['add-mac-cookie'] . "\n";
			$text.= "Mac Timeout  :" . $baris['mac-cookie-timeout'] . "\n";
			$text.= "Bytes-out    :" . $baris['bytes-out'] . "\n";
			$text.= "Rate-limit   :" . $baris['rate-limit'] . "\n=======================\n";
		}
	}
	$kirim = "<code>" . $text . "</code>";
	$options = ['reply' => true,'parse_mode' => 'html',];
	return Bot::sendMessage($kirim, $options);
});
$bot->cmd('/Traceroute|/traceroute', function ($do) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	$texts = "Mohon ditunggu permintaan sedang diproses";
	if(empty($do)){
		$text="Masukan alamat untuk ditaceroute\nContoh :\n\n/Traceroute detik.com\n/Traceroute google.com";
	}
	Bot::sendMessage($texts);
	if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
		$ARRAY = $API->comm("/tool/traceroute",array(
			"address" => "$do",
			"count" => "1",
			"use-dns" => "yes"
		));
		sleep(1);
		$textS.= "Traceroute dimulai\n";
		$textS.= "<b>Jumlah hop 💁‍♂️ ".count($ARRAY)."</b>\n";
		$data = $ARRAY;
		foreach ($data as $index => $hasil) {
			$text.= "address  :" . $hasil['address'] . "\n";
			$text.= "loss     :" . $hasil['loss'] . "\n";
			$text.= "sent     :" . $hasil['sent'] . "\n";
			$text.= "last     :" . $hasil['last'] . "\n";
			$text.= "avg      :" . $hasil['avg'] . "\n";
			$text.= "best     :" . $hasil['best'] . "\n";
			$text.= "worst    :" . $hasil['worst'] . "\n";
			$text.= "Status   :" . $hasil['status'] . "\n=======================\n";
		}
	}
	$kirim = "$textS\n<code>".$text."</code>";
	$arr2 = str_split($kirim, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$text = $arr2[$i];
		$options = ['reply' => true,'parse_mode' => 'html',];
		Bot::sendMessage($text, $options);
	}
});
$bot->cmd('/ppp|/PPP|/Ppp|!PPP', function ($active) {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	if ($active == "Secret") {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/ppp/secret/print");
			$data = $ARRAY;
			$text.= "<b>PPP SECRET</b> : (".count($ARRAY).")\n=====================\n";
			foreach ($data as $datas) {
				$text.= "<code>Name     : ".$datas['name']."</code>\n";
				$text.= "<code>Service  : ".$datas['service']."</code>\n";
				$text.= "<code>Caller   : ".$datas['caller-id']."</code>\n";
				$text.= "<code>Password : ".$datas['password']."</code>\n";
				$text.= "<code>Profile  : ".$datas['profile']."</code>\n";
				$text.= "<code>Loc addr : ".$datas['local-address']."</code>\n";
				$text.= "<code>Rem addr : ".$datas['remote-address']."</code>\n";
				$text.= "<code>Limit in : ".formatBites($datas['limit-bytes-in'])."</code>\n";
				$text.= "<code>Limit out: ".formatBites($datas['limit-bytes-out'])."</code>\n";
				$data = $datas['disable'];

				if ($data == "true") {
					$text.= "<code>Disable  : iya</code>\n";
				} else {
					$text.= "<code>Disable  : Tidak</code>\n";
				}
				$ids = $datas['.id'];
				$dataid = str_replace('*', 'id', $ids);
				$text.= "Remove /reMopsEc$dataid\n";
				$text.= "=====================\n";

			}
		} else {
			$textaaa = "Tidak dapat Terhubung Dengan Router Coba Kembali";
			Bot::sendMessage($textaaa);
		}
	} elseif ($active == "Profile") {
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/ppp/profile/print");
			$data = $ARRAY;
			$text.= "<b>PPP PROFILE</b> : (".count($ARRAY).")\n=====================\n";
			foreach ($data as $datas) {
				$text.= "<code>Name        : ".$datas['name']."</code>\n";
				$text.= "<code>Mpls        : ".$datas['use-mpls']."</code>\n";
				$text.= "<code>Compression : ".$datas['use-compression']."</code>\n";
				$text.= "<code>Only-one    : ".$datas['only-one']."</code>\n";
				$text.= "<code>Change-tcp  : ".$datas['change-tcp-mss']."</code>\n";
				$text.= "<code>Use-upnp    : ".$datas['use-upnp']."</code>\n";
				$text.= "<code>On-up       : ".$datas['on-up']."</code>\n";
				$text.= "<code>Limit in    : ".$datas['lon-up']."</code>\n";
				$text.= "<code>Limit out   : ".$datas['on-down']."</code>\n";
				$data = $datas['default'];

				if ($data == "true") {
					$text.= "<code>Default  : iya</code>\n";
				} else {
					$text.= "<code>Default  : Tidak</code>\n";
				}
				$ids = $datas['.id'];
				$dataid = str_replace('*', 'id', $ids);
				$text.= "Remove /reMopro$dataid\n";
				$text.= "=====================\n";

			}
		} else {
			$textaaa = "Tidak dapat Terhubung Dengan Router Coba Kembali";
				Bot::sendMessage($textaaa);
		}
	}else {
		$texta = "PPP Secret or PPP Profile\n";
		$keyboard = [['!PPP Secret', '!PPP Profile'], ['Help','!Hide'],];
				$replyMarkup = ['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true];
				$options = [
					'reply' => true,
					'reply_markup' => json_encode($replyMarkup),

				];
				Bot::sendMessage($texta, $options);
	}
	$arr2 = str_split($text, 4000);
	$amount_gen = count($arr2);
	for ($i = 0;$i < $amount_gen;$i++) {
		$options = ['parse_mode' => 'html'];
		return Bot::sendMessage($arr2[$i], $options);
	}
}); 
$bot->cmd('!Hide', function () {
	$text = "disembunyikan";
	$replyMarkup = ['keyboard' => [], 'remove_keyboard' => true, 'selective' => false,];
	$anu['reply_markup'] = json_encode($replyMarkup);
	return Bot::sendMessage($text, $anu);
});
$bot->cmd('!Help|/help|help|/Help', function () {
	$text.= "Apa yang bisa saya bantu?\n\n";
	$text.= "Mikbotam Adalah bot yang dapat berinteraksi dengan Mikrotik\n\n/Menu untuk Melihat Menu keyboard\n";
	$text.= "/Monitor - Monitoring Wifi\n";
	$text.= "/Ping    - PING local or networks\n";
	$text.= "/Dhcp    - Melihat Menu Dhcp\n";
	$text.= "/Address - Melihat IP Address\n";
	$text.= "/Pool    - Melihat Pool Address\n==========================\n";
	$text.= "/Traffic - Laporan Traffic\n";
	$text.= "/Interface - Menu Interface\n";
	$text.= "/Dns     - Melihat DNS \n";
	$text.= "/PPP  - Menu PPP\n";
	$text.= "/qrcode    - Terjemahkan qrcode \n";
	$text.= "/Hotspot - Hotspot Menu\n";
	$text.= "/Resource - Melihat Resource \n";
	$text.= "/Neighbor - Melihat Neighbor\n";
	$text.= "/Ipbinding- Melihat Binding Hotspot\n";
	$text.= "/Userprofile - Melihat profil User\n";
	$text.= "/Userbyprofile - Melihat user menurut profil\n==========================\n";
	$text.= "!User  - Melihat user\n";
	$text.= "+User  - Menambahakn User secara Singgle\n";
	$text.= "-User  - Comingsoon\n";
	$text.= "/rEm0vid - Remove Hotspot user\n";
	$text.= "/reMopsEcid - Remove Secret user\n==========================\n";
	$text.= "/Reboot - Comingsoon ⚠\n";
	$text.= "/reset - Comingsoon ⚠\n";
	$text.= "/Btest - Comingsoon ⚠\n==========================\n";
	
	Bot::sendMessage($text);
});
$bot->cmd('!Menu|/Menu', function () {
	$text.= "Menu Keyboard\n";
	$keyboard = [['!Monitor', 'Ping google.com'], ['!Hotspot','!interface'], ['!Traffic', '!Hide'],];
	$replyMarkup = ['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true, 'selective' => true,];
	$options = [
		'reply' => true,
		'reply_markup' => json_encode($replyMarkup),
	];
	Bot::sendMessage($text, $options);
});
$bot->cmd('/update', function () {
$respon=file_get_contents('http://core.sengkunibot.com/update/update.php');
$json=json_decode($respon,true);
$text=$json['text'];
$url=$json['url'];
	$keyboard[] = [['text' => 'Download', 'url' => $url], ['text' => 'Test bot', 'url' => 'https://t.me/testingbotmikrotik'],];
	$options = ['reply_markup' => ['inline_keyboard' => $keyboard],];
	return Bot::sendMessage($text, $options);
});
 //TAMBAHAKAN DISINI UNTUK CASTOM PERINTAH///

//Bonus manage Grup bot wajib menjadi admin group
$bot->cmd('!Pin', function () {
	$info = bot::message();
	$id = $info['chat']['id'];
	$iduser = $info['from']['id'];
	$msgidfrom = $info['message_id'];
	$msgid = $info['reply_to_message']['message_id'];
	$data = [
		'id' => $id,
		'message_id' => $msgid,
	];
	Bot::pinChatMessage($data);
	sleep(2);
	$data = ['id' => $id, 'message_id' => $msgidfrom,];
	Bot::deleteMessage($data);

}); //pin message
$bot->on('new_chat_member', function () {
	$info 		= bot::message();
	$NameGrup 	= $info['chat']['title'];
	$chatid 		= $info['chat']['id'];
	$id 			= $info['new_chat_member']['id'];
	$first_name = $info['new_chat_member']['first_name'];
	$messageid  = $info['message_id'];
	$text = "Selamat datang\n💝 $first_name\n\nAnda Saat ini Berada digrub\n💁‍♀️ $NameGrup";
	return Bot::sendMessage($text);
}); //set welcome new user
$bot->on('pinned_message', function () {
	$info 			= bot::message();
	$from 			= $info['from']['first_name'];
	$fromid 			= $info['from']['id'];
	$titlegrub  	= $info['chat']['username'];
	$fromtagname 	= $info['pinned_message']['from']['first_name'];
	$fromtagid 		= $info['pinned_message']['from']['id'];
	$fromtagIDMSG 	= $info['pinned_message']['message_id'];
	$id 				= $info['chat']['id'];
	$iduser 			= $info['from']['id'];
	$msgidfrom 		= $info['message_id'];
	$data = ['id' => $id, 'message_id' => $msgidfrom,];
	Bot::deleteMessage($data);
	$text = "Pesan Barus saja DiPinned\nOleh : <a href='tg://user?id=" . $fromid . "'>" .  $from . "</a>\nAsal Pesan : <a href='tg://user?id=" . $fromtagid . "'>" .  $fromtagname . "</a>\n";
	$URL = "https://t.me/$titlegrub/".$fromtagIDMSG;
	$keyboard[] = [['text' => '💝 Pinned Message', 'url' => $URL,],];
	$options = [
		'reply_markup' => [
			'inline_keyboard' => $keyboard
		],
		'parse_mode' => 'html'
	];
	return Bot::sendMessage($text, $options);
}); //set new pin change
$bot->regex('/^[Aa][Ss][Uu]/', function () {
	//spam message
	$info = bot::message();
	$id = $info['chat']['id'];
	$iduser = $info['from']['id'];
	$msgid = $info['message_id'];

	$data = [
		'id' => $id,
		'message_id' => $msgid,
	];

	$texta = Bot::deleteMessage($data);
}); //hapus kata as*
$bot->regex('/^[Nn][Jj][Ii][Rr]/', function () {
	//spam message
	$info = bot::message();
	$id = $info['chat']['id'];
	$iduser = $info['from']['id'];
	$msgid = $info['message_id'];

	$data = [
		'id' => $id,
		'message_id' => $msgid,
	];

	$texta = Bot::deleteMessage($data);
}); //hapus kata *njir

//Other bonus Comingsoon
//===========================================CommandHapusDisini==================================================//
$bot->regex('/^\/rEm0vid/', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	$mess = Bot::Message();
	$isi = $mess['text'];
	if ($isi == '/rEm0vid') {
		$text.= "⛔ Gagal dihapus \n\n<b>KETERANGAN   :</b>\nTidak Ditemukan Id User";
	} else {
		$id = str_replace('/rEm0vid', '*', $isi);
		$ids = str_replace('@Tesuibot', '', $id);
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY = $API->comm("/ip/hotspot/user/print", array("?.id" => $ids,));
			$data1 = $ARRAY[0]['.id'];
			$data1 = $ARRAY[0]['profile'];
			$data2 = $ARRAY[0]['name'];
			$data3 = $ARRAY[0]['password'];
			$ARRAY2 = $API->comm("/ip/hotspot/user/remove", array("numbers" => $ids,));
			$texta = json_encode($ARRAY2);
			if (strpos(strtolower($texta), 'no such item') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus \nUser tidak ditemukan \nMohon periksa kembali  \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($texta), 'invalid internal item number') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus \nId user tidak ditemuakn \Mohon periksa kembali\n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($texta), 'default trial user can not be removed') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus Default trial tidak dapat dihapus\n\n<b>KETERANGAN   :</b>\n$gagal";
			} else {
				$text.= "✔ User ini Berhasil Dihapus\n\n";
				$text.= "<code>ID      : $ids</code>\n";
				$text.= "<code>Profil  : $data1</code>\n";
				$text.= "<code>Name    : $data2</code>\n";
				$text.= "<code>Password: $data3</code>\n\n";
				sleep(2);
				$ARRAY3 = $API->comm("/ip/hotspot/user/print");
				$jumlah = count($ARRAY3);
				$text.= "Jumlah user saat ini : $jumlah user";
			}
		} else {
			$text = "Gagal Periksa sambungan Kerouter";
		}
	}
	$options = ['reply' => true,'parse_mode' => 'html',];
	$texta = json_encode($ARRAY2);
	return Bot::sendMessage($text, $options);
});
//Comingsoon
$bot->regex('/^\/reMopsEcid/', function () {
	global $datasa;
	$mikrotik_ip 		 = $datasa['ipaddress'];
	$mikrotik_username = $datasa['user'];
	$mikrotik_password = $datasa['password'];
	$API = new routeros_api();
	$mess = Bot::Message();
	$isi = $mess['text'];
	if ($isi == '/reMopsEcid') {
		$text.= "⛔ Gagal dihapus \n\n<b>KETERANGAN   :</b>\nTidak Ditemukan Id User";
	} else {
		$id = str_replace('/reMopsEcid', '*', $isi);
		$ids = str_replace('@Tesuibot', '', $id);
		if ($API->connect($mikrotik_ip, $mikrotik_username, $mikrotik_password)) {
			$ARRAY2 = $API->comm("/ppp/secret/remove", array("numbers" => $ids,));
			$texta = json_encode($ARRAY2);
			if (strpos(strtolower($texta), 'no such item') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus \nUser tidak ditemukan \nMohon periksa kembali  \n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($texta), 'invalid internal item number') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus \nId user tidak ditemuakn \Mohon periksa kembali\n\n<b>KETERANGAN   :</b>\n$gagal";
			} elseif (strpos(strtolower($texta), 'default trial user can not be removed') !== false) {
				$gagal = $ARRAY2['!trap'][0]['message'];
				$text.= "⛔ Gagal dihapus\nDefault trial tidak dapat dihapus\n\n<b>KETERANGAN   :</b>\n$gagal";
			} else {
				$text.= "Komandan, User ini Berhasil Dihapus\n\n";
				sleep(2);
				$ARRAY3 = $API->comm("/ppp/secret/print");
				$jumlah = count($ARRAY3);
				$text.= "Jumlah user saat ini : $jumlah user";
			}
		} else {
			$text = "Gagal Periksa sambungan Kerouter";
		}
	}
	$options = ['reply' => true,'parse_mode' => 'html',];
	$texta = json_encode($isi);
	return Bot::sendMessage($text, $options);
});

$bot->run();