<div class="sms_net_bd_wrapper">
    <div class="tabbed_section">
        <?php include_once SMS_NET_BD_PATH . '/partials/nav.php'; ?>
        <div class="tabbed_section_content_wrap">
            <div class="tabbed_section_content tabbed_section_content_active">

                <div class="custom-container">


                    <?php

                    if ($_POST['submit']) {

                        try {

                            $update = [
                                "sender_id" => $_POST['sender_id']
                            ];

                            $condition = substr_count($_POST['api_key'], '*') == 24;

                            if (!empty($_POST['api_key']) && !$condition) {

                                $send = new sms_net_bd($_POST['api_key']);

                                $res = $send->getBalance();

                                if ($res['error'] !== 0) {

                                    throw new Exception("API Key is not valid");
                                }

                                $update['sender_id'] = "";
                            }

                            if (!$condition) {
                                $update['api_key'] = $_POST['api_key'];
                                $update['sender_id'] = "";
                            }

                            update_query("sms_net_bd_settings", $update, "id = 1");


                            showAlert("Saved Successfully", "success");
                        } catch (\Throwable $th) {

                            showAlert($th->getMessage(), "danger");
                        }
                    }


                    $class = new Functions();

                    $settings = $class->getSettings();

                    $balance = null;

                    if (!empty($settings['api_key'])) {

                        $send = new sms_net_bd($settings['api_key']);

                        $res = $send->getBalance();

                        if ($res['error'] == 0) {
                            $balance = $res['data']['balance'];
                        }
                    }


                    function processApiKey($api_key)
                    {
                        //if empty return
                        if (empty($api_key)) {
                            return $api_key;
                        }

                        return substr_replace($api_key, str_repeat('*', 24), 12, 16);
                    }

                    try {

                        $send = new sms_net_bd($settings['api_key']);

                        $res = $send->getSenderID();

                        $serder_ids = $res['data']['items'];
                    } catch (\Throwable $th) {
                        $serder_ids = [];
                    }




                    ?>

                    <form method="post" action="">

                        <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
                            <tbody>

                                <tr>
                                    <td></td>
                                    <td class="fieldarea">
                                        <div class="logo form-control input-400">

                                            <img src="data:image/png;base64,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" alt="logo" width="300">

                                            <div class="social-links">
                                                <a href="https://github.com/smsnetbd/" target="_blank"><i class="fab fa-github"></i></a>
                                                <a href="https://www.sms.net.bd" target="_blank"><i class="fas fa-link"></i></a>
                                                <a href="https://portal.sms.net.bd/login/" target="_blank"><i class="fas fa-sign-in-alt"></i></a>

                                            </div>
                                        </div>

                                        <!-- social links -->



                                    </td>
                                </tr>

                                <tr>

                                    <td width="15%" class="fieldlabel">API KEY</td>
                                    <td class="fieldarea"><input type="text" class="form-control input-400" name="api_key" value="<?= processApiKey($settings['api_key']); ?>" tabindex="1"></td>

                                </tr>

                                <?php if (!empty($settings['api_key'])) : ?>




                                    <tr>

                                        <td class="fieldlabel">Sender ID</td>
                                        <td class="fieldarea">

                                            <select name="sender_id" class="form-control input-400" tabindex="2">
                                                <option value="" <?= $settings['sender_id'] == "" ? 'selected' : ''; ?>>None</option>
                                                <?php if ($serder_ids) : ?>
                                                    <?php foreach ($serder_ids as $sender_id) : ?>
                                                        <option value="<?= $sender_id['sender_id']; ?>" <?= $settings['sender_id'] == $sender_id['sender_id'] ? 'selected' : ''; ?>><?= $sender_id['sender_id']; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>

                                        </td>

                                    </tr>


                                <?php endif; ?>




                                <?php if (!empty($balance)) : ?>

                                    <tr>
                                        <td class="fieldlabel">Balance</td>
                                        <td class="fieldarea">BDT <?= $balance; ?></td>
                                    </tr>

                                <?php endif; ?>


                                <?php if (empty($settings['api_key'])) : ?>
                                    <tr>
                                        <td></td>

                                        <td class="fieldarea">
                                            <?= "Don't have an account? <a class='text-primary' href='https://www.sms.net.bd/signup/'>Register Now</a> (Free SMS Credit after Sign-up)." ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>


                            </tbody>
                        </table>

                        <div class="btn-container">
                            <input type="submit" value="Save Changes" class="btn btn-primary" tabindex="52" name="submit">
                        </div>
                    </form>



                </div>

            </div>
        </div>
    </div>
</div>