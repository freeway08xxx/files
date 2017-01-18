<section class="tabs">
    <ul class="nav nav-tabs" id="result_tabs">
        <li class="active">
            <a href="#ydn_rule" role="tabs" aria-controls="ydn_rule" data-toggle="tab">YDN(ルール)</a>
        </li>
        <li class="ydn_combination">
            <a href="#ydn_combination" role="tabs" aria-controls="ydn_combination" data-toggle="tab">YDN(組み合わせ)</a>
        </li>
        <li class="google_other_combination">
            <a href="#google_other_combination" role="tabs" aria-controls="google_other_combination" data-toggle="tab">Google(組み合わせ以外)</a>
        </li>
        <li class="google_combination">
            <a href="#google_combination" role="tabs" aria-controls="google_combination" data-toggle="tab">Google(組み合わせ)</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="ydn_rule">
            <table id="userlist_ydn_rule" class="display">
                <thead>
                    <tr>
                        <? foreach ($header_list_ydn_rule as $header) { ?>
                            <th><?= $header ?></th>
                        <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? if(isset($userlist_ydn_rule)) { ?>
                        <? foreach ($userlist_ydn_rule as $data) { ?>
                        <tr>
                            <td><?= $data["list_id"] ?></td>
                            <td><?= $data["status"] ?></td>
                            <td><?= $data["reach"] ?></td>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["name"] ?></td>
                            <td><?= $data["open"] ?></td>
                            <td><?= $data["reachperiod"] ?></td>
                            <td><?= $data["preset"] ?></td>
                            <? foreach ($data["rule"] as $rule) { ?>
                                    <td><?= $rule["group_no"] ?></td>
                                    <td><?= $rule["type"] ?></td>
                                    <td><?= $rule["compareOperator"] ?></td>
                                    <td><?= $rule["value"] ?></td>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="ydn_combination">
            <table id="userlist_ydn_combination" class="display">
                <thead>
                    <tr>
                        <? foreach ($header_list_ydn_combination as $header) { ?>
                            <th><?= $header ?></th>
                        <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? if(isset($userlist_ydn_combination)) { ?>
                        <? foreach ($userlist_ydn_combination as $data) { ?>
                        <tr>
                            <td><?= $data["list_id"] ?></td>
                            <td><?= $data["status"] ?></td>
                            <td><?= $data["reach"] ?></td>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["name"] ?></td>
                            <? foreach ($data["combinations"] as $combination) { ?>
                                    <td><?= $combination["group_no"] ?></td>
                                    <td><?= $combination["logical_operator"] ?></td>
                                    <td><?= $combination["target_list_name"] ?></td>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="google_other_combination">
            <table id="userlist_google_other_combination" class="display">
                <thead>
                    <tr>
                        <? foreach ($header_list_google as $header) { ?>
                            <th><?= $header ?></th>
                        <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? if(isset($userlist_google_other_combination)) { ?>
                        <? foreach ($userlist_google_other_combination as $data) { ?>
                        <tr>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["list_id"] ?></td>
                            <td><?= $data["name"] ?></td>
                            <td><?= $data["status"] ?></td>
                            <td><?= $data["membershipLifeSpan"] ?></td>
                            <td><?= $data["size"] ?></td>
                            <td><?= $data["type"] ?></td>
                            <? foreach ($data["condition"] as $condition) { ?>
                                    <td><?= $condition["group_no"] ?></td>
                                    <td><?= $condition["type"] ?></td>
                                    <td><?= $condition["compareOperator"] ?></td>
                                    <td><?= $condition["value"] ?></td>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                            <td><?= $data["tag_id"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="google_combination">
            <table id="userlist_google_combination" class="display">
                <thead>
                    <tr>
                        <? foreach ($header_list_google_combination as $header) { ?>
                            <th><?= $header ?></th>
                        <? } ?>
                    </tr>
                </thead>
                <tbody>
                    <? if(isset($userlist_google_combination)) { ?>
                        <? foreach ($userlist_google_combination as $data) { ?>
                        <tr>
                            <td><?= $data["list_id"] ?></td>
                            <td><?= $data["status"] ?></td>
                            <td><?= $data["size"] ?></td>
                            <td><?= $data["account_id"] ?></td>
                            <td><?= $data["name"] ?></td>
                            <? foreach ($data["condition"] as $combination) { ?>
                                    <td><?= $combination["group_no"] ?></td>
                                    <td><?= $combination["logical_operator"] ?></td>
                                    <td><?= $combination["target_list_name"] ?></td>
                            <? } ?>
                            <td><?= $data["description"] ?></td>
                        </tr>
                        <? } ?>
                    <? } ?>
                </tbody>
            </table>
        </div>
    </div>

</section>
