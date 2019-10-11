<?php
$this->load->view('site/templates/profile_header');
?> 

<div class="rider-signup">
    <div class="container-new">
        <section>
            <div class="col-md-12 profile_rider">
                <?php
                $this->load->view('site/templates/profile_sidebar');
                ?>
                <div class="col-md-9 nopadding profile_rider_right">
                    <div class="col-md-12 rider-pickup-detail">
                        <h2> <?php if ($this->lang->line('user_language_settings') != '') echo stripslashes($this->lang->line('user_language_settings')); else echo 'LANGUAGE SETTINGS'; ?></h2>
                        <div class="col-md-12 lang-head">
                            <h3><?php if ($this->lang->line('user_choose_language_to_set') != '') echo stripslashes($this->lang->line('user_choose_language_to_set')); else echo 'Choose language to set your preference'; ?></h3>
                        </div>
                        <div class="language-list">
                            <form action="rider/language-settings" method="post" id="languageForm">
                                <?php
                                if ($languageList->num_rows() > 0) {
                                    foreach ($languageList->result() as $langs) {
                                        $actClass = '';
                                        if ($userLangCode == $langs->lang_code) {
                                            $actClass = 'active-lang';
                                        }
                                        ?>
                                        <label id="longLbl-<?php echo $langs->lang_code; ?>" class="longLbl <?php echo $actClass; ?>" onclick="chooseLanguage('<?php echo $langs->lang_code; ?>');">
                                            <?php echo $langs->name; ?>
                                            <span class="selecterLbl" id="lonSelecter-<?php echo $langs->lang_code; ?>">
                                                <?php if ($actClass != '') { ?>
                                                    <a class="c-active"> </a>
                                                <?php } ?>
                                            </span>
                                        </label>
                                        <input id="lang-<?php echo $langs->lang_code; ?>" type="radio" name="language_code" value="<?php echo $langs->lang_code; ?>"/>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <label class="active-lang"><?php echo $dLangName; ?>
                                        <input id="lang-<?php echo $dLangCode; ?>" type="radio" name="language_code" value="<?php echo $dLangCode; ?>"/>
                                        <a class="c-active"> </a>
                                    </label>
                                    <?php
                                }
                                ?>
                                <br/>
                                <button type="submit" class="btn btn-default money-btn" id="payBtn" onclick="wallet_payment_amt_validate('manual');"><?php if ($this->lang->line('user_save_settings') != '') echo stripslashes($this->lang->line('user_save_settings')); else echo 'SAVE SETTINGS'; ?></button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div> 

<script>
    function chooseLanguage(lid) {
        $(".longLbl").each(function () {
            $(this).removeClass("active-lang");
        });
        $(".selecterLbl").each(function () {
            $(this).html('');
        });
        $('#longLbl-' + lid).addClass('active-lang');
        $('#lonSelecter-' + lid).html('<a class="c-active"> </a>');
    }
</script>

<style>
    .language-list {
        text-align: center;
    }

    .language-list label {
        color: #2e2d2d;
        font-family: 'ralewaybold';
        font-size: 13px;
        font-weight: normal;
        padding: 8px 26px;
        text-transform: uppercase;
        text-shadow: none;
        text-align: center;
        background: #EEE;
        margin: 1%;
        width: 50%;
        cursor: pointer;
    }

    .language-list input[type="radio"]{
        display:none !important;
    }


    .active-lang {
        color: #07b5e6 !important;
    }

    .language-list label:hover {
        color: #07b5e6;
    }

    .lang-head {
        text-align: center;
        color: #666;
        font-family: 'LatoBold';
        font-size: 16px;
        margin-bottom: 10px;
    }


    .language-list button {
        float: none;
        display: inline-block;
        width: 50%;
        /* border: 1px solid #28CBF9; */
        background-color: #28CBF9;
        color: #fff;
        margin-top: 15px;
        background-image: none;
        box-shadow: none;
        text-shadow: none;
        font-family: 'LatoBold';
        font-size: 16px;
        height: 35px;
    }

</style>

<?php
$this->load->view('site/templates/footer');
?> 