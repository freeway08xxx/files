<div class="legacy">
    <input type="hidden" id="nav_id" name="nav_id" value="get" placeholder="">

    <form method="post" name="form01">
        <!-- クライアント選択 START -->
        <div class="clearfix select-client">
            <fieldset class="columns">
                <legend>クライアント選択（<?= count($DB_client_list); ?>件）</legend>
                <select id="client_id" name="client_id">
                    <option value="">--</option>
                    <? foreach ($DB_client_list as $DB_client) { ?>
                        <? if (!empty($OUT_data["client_id"])) { ?>
                            <option value="<?= $DB_client["id"] ?>" <? if ($DB_client["id"] === $OUT_data["client_id"]) { print "selected"; } ?>><?= $DB_client["company_name"] ?><? if ($DB_client["client_name"]) echo "//" . $DB_client["client_name"]; ?></option>
                        <? } else { ?>
                            <option value="<?= $DB_client["id"] ?>"><?= $DB_client["company_name"] ?><? if ($DB_client["client_name"]) echo "//" . $DB_client["client_name"]; ?></option>
                        <? } ?>
                    <? } ?>
                </select>
            </fieldset>
        </div>
        <!-- クライアント選択済み -->
        <? if (!empty($OUT_data["client_id"])) { ?>
            <!-- アカウント選択 START -->
            <?= $HMVC_accounttable; ?>
            <!-- アカウント選択 END -->
        <? } ?>
    </form>

</div>

<?= Asset::js('userlist/main.js') ?>