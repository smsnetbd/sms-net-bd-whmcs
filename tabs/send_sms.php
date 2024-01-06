<script>
    $(document).ready(function() {
        if (typeof WHMCS.selectize !== "undefined") {
            jQuery('.selectize-client-search').data('search-url', 'index.php?rp=/admin/search/client');
            WHMCS.selectize.clientSearch();
        } else {
            var clientDropdown = jQuery(".selectize-client-search");

            var clientSearchSelectize = clientDropdown.selectize({
                plugins: ['whmcs_no_results'],
                valueField: clientDropdown.data('value-field'),
                labelField: 'name',
                searchField: ['name', 'email', 'companyname'],
                create: false,
                maxItems: 1,
                //preload: 'focus',
                optgroupField: 'status',
                optgroupLabelField: 'name',
                optgroupValueField: 'id',
                optgroups: [{
                        $order: 1,
                        id: 'active',
                        name: clientDropdown.data('active-label')
                    },
                    {
                        $order: 2,
                        id: 'inactive',
                        name: clientDropdown.data('inactive-label')
                    }
                ],
                render: {
                    item: function(item, escape) {
                        if (typeof dropdownSelectClient == "function") {
                            dropdownSelectClient(
                                escape(item.id),
                                escape(item.name) + (item.companyname ? ' (' + escape(item.companyname) + ')' : '') +
                                (item.id > 0 ? ' - #' + escape(item.id) : ''),
                                escape(item.email)
                            );
                        }
                        return '<div><span class="name">' + escape(item.name) +
                            (item.companyname ? ' (' + escape(item.companyname) + ')' : '') +
                            (item.id > 0 ? ' - #' + escape(item.id) : '') + '</span></div>';
                    },
                    option: function(item, escape) {
                        return '<div><span class="name">' +
                            escape(item.name) + (item.companyname ? ' (' + escape(item.companyname) + ')' : '') +
                            (item.id > 0 ? ' - #' + escape(item.id) : '') + '</span>' +
                            (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') + '</div>';
                    }
                },
                load: function(query, callback) {
                    jQuery.ajax({
                        url: getClientSearchPostUrl(),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            dropdownsearchq: query,
                            clientId: currentValue
                        },
                        error: function() {
                            callback();
                        },
                        success: function(res) {
                            callback(res);
                        }
                    });
                },
                score: function(search) {
                    var score = this.getScoreFunction(search);
                    return function(item) {
                        var thisScore = score(item);
                        if (thisScore && item.status === 'inactive') {
                            thisScore = 0.0000001;
                        }
                        return thisScore;
                    };
                },

                onFocus: function() {
                    currentValue = clientSearchSelectize.getValue();
                    clientSearchSelectize.clear();
                },
                onBlur: function() {
                    if (clientSearchSelectize.getValue() == '' || clientSearchSelectize.getValue() < 1) {
                        clientSearchSelectize.setValue(currentValue);
                    }
                }
            });
            var currentValue = '';

            if (clientSearchSelectize.length) {
                /**
                 * selectize assigns any items to an array. In order to be able to run additional
                 * functions on this (like auto-submit and clear).
                 *
                 * @link https://github.com/brianreavis/selectize.js/blob/master/examples/api.html
                 */
                clientSearchSelectize = clientSearchSelectize[0].selectize;
            }
        }
    });



    /*
|-------------------------------------------
| Instant sms counter
|-------------------------------------------
*/
    $(document).on(
        'input change keyup propertychange paste',
        'textarea#smscontent',
        function() {
            let str = this.value.trim(),
                chars = str.length;
            let GSMC =
                '@Â£$Â¥Ã¨Ã©Ã¹Ã¬Ã²Ã‡\nÃ˜Ã¸\rÃ…Ã¥Î”_Î¦Î“Î›Î©Î Î¨Î£Î˜ÎžÃ†Ã¦ÃŸÃ‰ !"#Â¤%&\'()*+,-./0123456789:;<=>?Â¡ABCDEFGHIJKLMNOPQRSTUVWXYZÃ„Ã–Ã‘ÃœÂ§Â¿abcdefghijklmnopqrstuvwxyzÃ¤Ã¶Ã±Ã¼Ã ';
            let exGSMC = ['~', '^', '{', '}', '[', ']', '|', '\\', 'â‚¬'];
            let hasMoreThanAscii = [...str].some(
                (char) => !GSMC.includes(char) && !exGSMC.includes(char)
            );

            // if only gsm and has gsm ext characters chars++
            [...str].some(function(char) {
                !hasMoreThanAscii && exGSMC.includes(char) ? chars++ : false;
            });

            // normal sms
            let per_message = hasMoreThanAscii ? 70 : 160;

            // if multi sms
            if (chars > per_message) {
                per_message = hasMoreThanAscii ? 67 : 153;
            }

            let messages = Math.ceil(chars / per_message);
            let remaining = per_message * messages - chars;

            if (remaining === 0 && messages === 0) {
                remaining = per_message;
            }

            $('.charCount').html(
                `${chars} characters | ${remaining} characters left | ${messages} SMS (${per_message} Char./SMS)`
            );
        }
    );
</script>

<div class="sms_net_bd_wrapper">
    <div class="tabbed_section">
        <?php include_once SMS_NET_BD_PATH . '/partials/nav.php'; ?>
        <div class="tabbed_section_content_wrap">
            <div class="tabbed_section_content tabbed_section_content_active">
                <div class="custom-container">

                    <?php

                    if (!empty($_POST['action'] && $_POST['action'] == "send")) {


                        //if empty message
                        if (empty($_POST['message'])) {
                            showAlert("Message is empty", "danger");
                        } elseif (empty($_POST['userid'])) {
                            showAlert("Client not selected", "danger");
                        } else {

                            $class       = new SmsClass();

                            $user_id = $_POST['userid'];

                            $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`country`, `a`.`phonenumber` as `gsmnumber`
                                                                FROM `tblclients` as `a` where `a`.`id` = $user_id order by `a`.`firstname`";
                            $result      = mysql_query($userSql);
                            $data        = mysql_fetch_array($result);
                            $userid      = $data['id'];
                            $gsmnumber   = $data['gsmnumber'];
                            $replacefrom = array("{firstname}", "{lastname}");
                            $replaceto = array($data['firstname'], $data['lastname'], $company_details['CompanyName']);
                            $message     = str_replace($replacefrom, $replaceto, $_POST['message']);
                            $class->setNumber($gsmnumber);
                            $class->setMessage($message);
                            $class->setUserid($userid);

                            $result = $class->send();

                            if (!$result) {
                                showAlert("Message sending failed", "danger");
                            } else {
                                showAlert("Message sent successfully", "success");
                            }
                        }
                    }
                    ?>

                    <form action="" method="post">

                        <input type="hidden" name="action" value="save">
                        <div class="internalDiv">
                            <table class="form" width="100%" cellspacing="2" cellpadding="3">
                                <tbody>
                                    <tr>
                                        <td class="fieldlabel" width="30%">Client</td>
                                        <td class="fieldarea">
                                            <select id="selectUserid" name="userid" class="form-control selectize selectize-client-search" data-value-field="id" data-active-label="Active" data-inactive-label="Inactive" placeholder="Start Typing to Search Clients" required>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fieldlabel" width="30%">Message</td>
                                        <td class="fieldarea">
                                            <textarea rows="5" name="message" style="padding:5px" class="form-control textarea_message" id="smscontent" required></textarea>
                                            <div class="charCount text-right">0 characters | 160 characters left | 0 SMS (160 Char./SMS)</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fieldlabel" width="30%">Parameters :</td>
                                        <td class="fieldarea">
                                            {firstname},{lastname}<br>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fieldlabel" width="30%"></td>
                                        <td class="fieldarea"><button type="submit" class="save_btn btn btn-sm btn-primary" name="action" value="send">Send</button></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</div>