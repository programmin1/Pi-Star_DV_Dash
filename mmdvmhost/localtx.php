<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
//$localTXList = getHeardList($reverseLogLinesMMDVM);
$localTXList = $lastHeard;

?>
<b><?php echo $lang['local_tx_list'];?></b>
  <table>
    <tr>
      <th><a class="tooltip" href="#"><?php echo $lang['time'];?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['mode'];?><span><b>Transmitted Mode</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['callsign'];?><span><b>Callsign</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['target'];?><span><b>Target, D-Star Reflector, DMR Talk Group etc</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['src'];?><span><b>Received from source</b></span></a></th>
      <th><a class="tooltip" href="#"><?php echo $lang['dur'];?>(s)<span><b>Duration in Seconds</b></span></a></th>
      <th style="min-width:5ch"><a class="tooltip" href="#"><?php echo $lang['ber'];?><span><b>Bit Error Rate</b></span></a></th>
      <th style="min-width:8ch"><a class="tooltip" href="#">RSSI<span><b>Received Signal Strength Indication</b></span></a></th>
    </tr>
<?php
$counter = 0;
$i = 0;
for ($i = 0; $i < count($localTXList); $i++) {
		$listElem = $localTXList[$i];
		if ($listElem[5] == "RF" && ($listElem[1] == "D-Star" || startsWith($listElem[1], "DMR") || $listElem[1] == "YSF" || $listElem[1]== "P25" || $listElem[1]== "NXDN")) {
			if ($counter <= 19) { //last 20 calls
				$utc_time = $listElem[0];
                        	$utc_tz =  new DateTimeZone('UTC');
                        	$local_tz = new DateTimeZone(date_default_timezone_get ());
                        	$dt = new DateTime($utc_time, $utc_tz);
                        	$dt->setTimeZone($local_tz);
                        	$local_time = $dt->format('H:i:s M jS');
			echo"<tr>";
			echo"<td align=\"left\">$local_time</td>";
			echo"<td align=\"left\">$listElem[1]</td>";
			if (is_numeric($listElem[2]) || strpos($listElem[2], "openSPOT") !== FALSE) {
				echo "<td align=\"left\">$listElem[2]</td>";
			} elseif (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $listElem[2])) {
				echo "<td align=\"left\">$listElem[2]</td>";
			} else {
				if (strpos($listElem[2],"-") > 0) { $listElem[2] = substr($listElem[2], 0, strpos($listElem[2],"-")); }
				if ($listElem[3] && $listElem[3] != '    ' ) {
					//echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" data-featherlight=\"iframe\" data-featherlight-iframe-min-width=\"90%\" data-featherlight-iframe-max-width=\"90%\" data-featherlight-iframe-width=\"2000\" data-featherlight-iframe-height=\"2000\">$listElem[2]</a>/$listElem[3]</td>";
					//echo "<td align=\"left\"><a href=\"https://database.radioid.net/database/view?callsign=$listElem[2]\" target=\"popup\" onclick=\"window.open('https://database.radioid.net/database/view?callsign=$listElem[2]','popup','width=600,height=600'); return false;\">$listElem[2]</a>/$listElem[3]</td>";
					echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" target=\"_blank\">$listElem[2]</a>/$listElem[3]</td>";
				} else {
					//echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" data-featherlight=\"iframe\" data-featherlight-iframe-min-width=\"90%\" data-featherlight-iframe-max-width=\"90%\" data-featherlight-iframe-width=\"2000\" data-featherlight-iframe-height=\"2000\">$listElem[2]</a></td>";
					//echo "<td align=\"left\"><a href=\"https://database.radioid.net/database/view?callsign=$listElem[2]\" target=\"popup\" onclick=\"window.open('https://database.radioid.net/database/view?callsign=$listElem[2]','popup','width=600,height=600'); return false;\">$listElem[2]</a>/$listElem[3]</td>";
					echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" target=\"_blank\">$listElem[2]</a></td>";
				}
			}
			if (strlen($listElem[4]) == 1) { $listElem[4] = str_pad($listElem[4], 8, " ", STR_PAD_LEFT); }
			echo"<td align=\"left\">".str_replace(" ","&nbsp;", $listElem[4])."</td>";
			if ($listElem[5] == "RF"){
				echo "<td style=\"background:#1d1;\">RF</td>";
			} else {
				echo "<td>$listElem[5]</td>";
			}
			if ($listElem[6] == null) {
				// Live duration
				$utc_time = $listElem[0];
				$utc_tz =  new DateTimeZone('UTC');
				$now = new DateTime("now", $utc_tz);
				$dt = new DateTime($utc_time, $utc_tz);
				$duration = $now->getTimestamp() - $dt->getTimestamp();
				$duration_string = $duration<999 ? round($duration) . "+" : "&infin;";
				echo "<td colspan=\"3\" style=\"background:#f33;\">TX " . $duration_string . " sec</td>";
			} else if ($listElem[6] == "DMR Data") {
				echo "<td colspan=\"3\" style=\"background:#1d1;\">DMR Data</td>";
			} else {
				echo"<td>$listElem[6]</td>"; //duration
				
				// Colour the BER Field
				if (floatval($listElem[8]) == 0) { echo "<td>$listElem[8]</td>"; }
				elseif (floatval($listElem[8]) >= 0.0 && floatval($listElem[8]) <= 1.9) { echo "<td style=\"background:#1d1;\">$listElem[8]</td>"; }
				elseif (floatval($listElem[8]) >= 2.0 && floatval($listElem[8]) <= 4.9) { echo "<td style=\"background:#fa0;\">$listElem[8]</td>"; }
				else { echo "<td style=\"background:#f33;\">$listElem[8]</td>"; }

				echo"<td>$listElem[9]</td>"; //rssi
			}
			echo"</tr>\n";
			$counter++; }
		}
	}

?>
  </table>
