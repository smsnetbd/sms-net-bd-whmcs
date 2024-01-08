<div class="sms_net_bd_wrapper">
    <div class="tabbed_section">
        <?php include_once SMS_NET_BD_PATH . '/partials/nav.php'; ?>
        <div class="tabbed_section_content_wrap">
            <div class="tabbed_section_content tabbed_section_content_active">


                <table width="100%" class="datatable" border="0" cellspacing="1" cellpadding="3" style="margin: 0px; border: 0px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th>Sender ID</th>
                            <th>Mobile Number</th>
                            <th width="50%">Message</th>
                            <th>Date Time</th>
                            <th>Status</th>
                            <th width="300">Log</th>
                            <th width="20"></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php


                        //delete sms
                        if (isset($_GET['deletesms'])) {

                            try {
                                $id = $_GET['deletesms'];
                                // convert to int
                                $id = (int) $id;

                                $sql = "DELETE FROM `sms_net_bd_messages` WHERE `id` = {$id}";

                                mysql_query($sql);

                                showAlert("Deleted Successfully", "success");
                            } catch (\Throwable $th) {
                                showAlert($th->getMessage(), "danger");
                            }
                        }


                        function getStatus($data)
                        {

                            $status = $data['status'];

                            $class = new Functions();

                            $settings = $class->getSettings();

                            if ($status == "pending" && !empty($data['req_id'])  && !empty($settings['api_key'])) {

                                try {

                                    $send = new sms_net_bd($settings['api_key']);

                                    $resp = $send->getReport($data['req_id']);

                                    if ($resp['error'] == 0) {
                                        $status = $resp['data']['recipients'][0]['status'];
                                        return strtolower($status);
                                    }

                                    update_query("sms_net_bd_messages", ["status" => $status], "id = " . $data['id']);
                                } catch (\Throwable $e) {
                                    return $status;
                                }
                            }

                            return $status;
                        }


                        // Getting pagination values.
                        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $limit  = (isset($_GET['limit']) && $_GET['limit'] <= 50) ? (int)$_GET['limit'] : 10;
                        $start  = ($page > 1) ? ($page * $limit) - $limit : 0;
                        $order  = isset($_GET['order']) ? $_GET['order'] : 'DESC';
                        /* Getting messages order by date desc */
                        $sql    = "SELECT `m`.*,`user`.`firstname`,`user`.`lastname`, `user`.`id` as `uid`
                        FROM `sms_net_bd_messages` as `m`
                        LEFT JOIN `tblclients` as `user` ON `m`.`client_id` = `user`.`id`
                        ORDER BY `m`.`created` {$order} limit {$start},{$limit}";
                        $result = mysql_query($sql);
                        $i = 0;

                        //Getting total records
                        $total  = "SELECT count(id) as toplam FROM `sms_net_bd_messages`";
                        $sonuc  = mysql_query($total);
                        $sonuc  = mysql_fetch_array($sonuc);
                        $toplam = $sonuc['toplam'];

                        //Page calculation
                        $sayfa  = ceil($toplam / $limit);
                        $empty = true;

                        while ($data = mysql_fetch_array($result)) {
                            $empty = false;

                            $i++;

                            if ($data['error_details'] !== 0) {
                                $log =  $data['log_details'];
                            } else {
                                $log = "";
                            }


                            //Jan 1, 2024 12:00 AM
                            $formatedDate = date("M d, Y h:i A", strtotime($data['created']));

                        ?>

                            <tr>
                                <td><?php echo $data['id']; ?></td>
                                <td><a href="clientssummary.php?userid=<?php echo $data['uid']; ?>"><?php echo $data['firstname'] . ' ' . $data['lastname']; ?></a></td>
                                <td><?php echo empty($data['sender_id']) ? "Default" : $data['sender_id']; ?></td>
                                <td><?php echo $data['recipient']; ?></td>
                                <td><?php echo $data['message']; ?></td>
                                <td><?php echo $formatedDate; ?></td>
                                <td><?php echo getStatus($data) ?></td>
                                <td><?php echo $log; ?></td>

                                <td><a href="addonmodules.php?module=sms_net_bd&tab=sms_logs&deletesms=<?php echo $data['id']; ?>" title="Delete"><img src="images/delete.gif" width="16" height="16" border="0" alt="Delete"></a></td>
                            </tr>


                        <?php

                        }
                        if ($empty) {
                            echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                        }

                        $list = "";
                        for ($a = 1; $a <= $sayfa; $a++) {
                            $selected = ($page == $a) ? 'selected="selected"' : '';
                            $list .= "<option value='addonmodules.php?module=sms_net_bd&tab=sms_logs&page={$a}&limit={$limit}&order={$order}' {$selected}>{$a}</option>";
                        }
                        echo "<select  onchange=\"this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);\">{$list}</select></div>";


                        ?>
                    </tbody>
                </table>



            </div>
        </div>
    </div>
</div>