<?php
use pdima88\phpassets\Assets;
/** @var cmsTemplate $this */
    $pageTitle = 'Приобрести подписку';
    $this->setPageTitle($pageTitle);

    $this->addControllerCSS('paidaccess');
    $this->addTplCSSName('rspinner');
    if (!isset($selectedPlan)) {
        foreach($plans as $plan) {
            $selectedPlan = $plan['id'];
            break;
        }
    }
    $selectedTariffId = ($selectedTariff['id'] ?? 0);


?>
<style>
    .bonus_tariff_list {
        text-align: left;
        max-width: 600px;
        margin: 0 auto;
    }
</style>
<div class="container">
<h1><?= $pageTitle ?></h1>

<div id="paidaccessTariffs" class="tabs-menu">
    <h2>Выберите тарифный план:</h2>
<ul class="tabbed">
    <?php $selectedPlanIndex = 0; $i = 0;
    foreach ($plans as $plan): ?>
        <li style="bottom:-10px" role="presentation"<?php
        if ($selectedPlan == $plan['id']) {
            echo ' class="active"';
            $selectedPlanIndex = $i;
        } ?>">
            <a href="#p<?= $plan['id'] ?>" aria-controls="p<?= $plan['id'] ?>"
               role="tab" data-toggle="tab"><?= $plan['title'] ?></a></li>
    <?php $i++; endforeach; ?>
</ul>


    <form method="post">


    <?php $i = 0; foreach ($plans as $plan): ?>
    <div class="tab" id="p<?= $plan['id'] ?>"<?php if ($selectedPlanIndex != $i) { ?> style="display:none;"<?php } ?>>
        <div class="gui-panel">
        <h3><?= $plan['title'] ?></h3>
            <div class="paidaccess-tariffplan-description">
                <?php if ($plan['hint'] ?? false): ?>

                    <?= $plan['hint'] ?>

                <?php endif; ?>
            </div>
        </div>

        <table class="" style="margin:0 auto; max-width: 400px">
            <thead>
                <tr>
                <th></th>
                <th>Срок доступа</th>
                <th>Кол-во дней</th>
                <th>Количество вопросов экспертам</th>
                <th>Цена</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($plan['tariffs'] as $tariff): ?>
            <tr>
                <td><input type="radio" id="tariff<?= $tariff['id'] ?>"
                           class="paidaccessTarifRadioBtn"
                           <?= ($selectedTariffId == $tariff['id']) ? ' checked ' : ''?>
                           name="tariff_id" value="<?= $tariff['id'] ?>"></td>
                <td style="white-space: nowrap"><label for="tariff<?= $tariff['id'] ?>"><?= $tariff['name'] ?></label></td>
                <td style="text-align: center"><?= $tariff['period'] ?></td>
                <td style="text-align: center"><?= $tariff['questions'] ?></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($tariff['price'], 0, ',', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $i++; endforeach; ?>

</div>

<div id="paidaccessSelectedPrice" style="font-size: 24px; text-align: center;<?= $selectedTariff ? '': 'display:none' ?>"><br>
    <small>Выбран тарифный план <b id="lblSelectedTariffName"></b> со сроком доступа
    <b id="lblSelectedTariffPeriod"></b></small><br>
    Стоимость: <b id="lblSelectedTariffPrice"></b> сум

    <div id="paidaccessFree" style="display:none">
        <button type="submit" name="submit" value="free" class="button">Получить бесплатно</button>
    </div>

    <div id="paidaccessPay" style="display: none;">
        <button type="submit" name="submit" value="pay" class="button">Перейти к оплате</button>
    </div>

    <div id="paidaccessUseBonuscode" v-if="selectedTariff">
        <h3 class="paidaccess-use-bonuscode">Введите бонус-код, чтобы получить скидку:</h3>

        <input type="text" id="txtPaidaccessBonuscode" name="bonus" class="form-control" autocomplete="off"
               style="width: 300px; margin: 0 auto; display: inline-block">

        <span style="position: absolute;
    display: inline-block;
    width: 30px;
    height: 35px;"><span id="rspinnerBonuscode" class=""></span></span>

        <div id="paidaccessBonuscodeResult" style="display: none">
            <div class="result_ok">
                По этому бонус-коду предоставляется скидка <span class="discount_value"></span><br>
                Стоимость с учетом скидки: <b class="price_with_discount"></b> сум
            </div>

            <div class="invalid_tariff">
                Бонус-код действителен только для следующих тарифов:
                <div class="bonus_tariff_list">

                </div>
            </div>

            <div class="error_not_found alert alert-danger">
                Бонус-код не найден или срок действия истек
            </div>

            <div class="error_activated alert alert-danger">
                Бонус-код уже активирован
            </div>

            <div class="error_ajax alert alert-warning">
                Ошибка при запросе: <span class="error_msg"></span>
            </div>
        </div>

    </div>



    <div id="paidaccessActivateBonus" style="display: none;">
        <button type="submit" name="submit" value="bonus" class="button">Активировать бонус-код</button>
    </div>

</div>

    </form>

</div>

<script>
    $(function(){
        initTabs('#paidaccessTariffs', <?= $selectedPlanIndex ?>);

        var selectedTariffId = <?= $selectedTariffId ?>;
        var plans = <?= $plans ? json_encode($plans) : '[]' ?>;
        var tariffs = <?= $tariffs ? json_encode($tariffs) : '[]' ?>;
        if (selectedTariffId) setSelectedTariffId(selectedTariffId);

        $('#paidaccessTariffs .tabbed a').click(function (e) {
            e.preventDefault();
            //$(this).tab('show');
            var tabid = $(this).attr('aria-controls');
            var planid = tabid.substr(1);
            var selectedPlan = plans[planid];
            var switchToTariffId = $('#'+tabid+' .paidaccessTarifRadioBtn:last').val();
            var switchToTariff = tariffs[switchToTariffId];
            if (selectedPlan && selectedPlan.tariffs) {
                var selectedTariff = tariffs[selectedTariffId];
                if (selectedTariff) {
                    for (var t in selectedPlan.tariffs) {
                        var tariff = selectedPlan.tariffs[t];
                        if (tariff.period == selectedTariff.period) {
                            switchToTariff = tariff; break;
                        }
                    }
                }
            }

            if (switchToTariff) {
                switchTariff(switchToTariff.id);
            }
        });

        function switchTariff(id) {
            $('#tariff'+id).prop('checked', true);
            setSelectedTariffId(id);
        }

        function selectTariff(id) {
            var t = tariffs[id];
            if (t) {
                //$('#paidAccessTariffPlansTabs a[aria-controls="p'+t.plan_id+'"]').tab('show');
                $('#paidaccessTariffs ul.tabbed a[href = "#p'+t.plan_id+'"]').trigger('click');
            }
            switchTariff(id);
        }

        function selectTariffClick() {
            var id = $(this).attr('data-tariffId');
            selectTariff(id);
            return false;
        }

        function setSelectedTariffId(id) {
            selectedTariffId = id;
            if (selectedTariffId in tariffs) {
                var selectedTariff = tariffs[selectedTariffId];
                var selectedPlan = plans[selectedTariff['plan_id']];
                $('#lblSelectedTariffName').text(selectedPlan['title']);
                $('#lblSelectedTariffPeriod').text(selectedTariff['name']);
                $('#lblSelectedTariffPrice').text(selectedTariff['price']);
                $('#paidaccessSelectedPrice').show();
            } else {
                $('#paidaccessSelectedPrice').hide();
            }
            checkBonusCode();
        }

        var checkBonusCodeTimer = false;
        var currentBonusCode = '';
        var currentBonus = '';
        function queueCheckBonuscode() {

            if ($('#txtPaidaccessBonuscode').val() != currentBonusCode) {
                if (checkBonusCodeTimer) window.clearTimeout(checkBonusCodeTimer);
                checkBonusCodeTimer = window.setTimeout(checkBonuscodeRequest, 3000);
                $('#rspinnerBonuscode').addClass('rspinner rspinner-wait');
                $('#paidaccessBonuscodeResult').hide();
                showSubmitBtn(false);
            }
        }
        function checkBonuscodeRequest() {
            checkBonusCodeTimer = false;
            $('#rspinnerBonuscode').removeClass('rspinner-wait').addClass('rspinner-load');
            currentBonusCode = $('#txtPaidaccessBonuscode').val();
            currentBonus = false;
            $('#paidaccessBonuscodeResult > div').hide();

            $.ajax({
              url: "<?= $this->href_to('bonus') ?>",
              data: {
                  code: currentBonusCode,
                  tariff_id: selectedTariffId
              },
              cache: false,
              type: 'POST',
              complete: function(xhr, status) {
                  $('#paidaccessBonuscodeResult').show();
                  $('#rspinnerBonuscode').removeClass('rspinner rspinner-wait rspinner-load');
              },
              success: function(data, status, xhr) {
                if (data.reload) {
                    location.reload();
                } else if (data.error) {
                    $('#paidaccessBonuscodeResult > .error_'+data.error).show();
                    currentBonus = false;
                    checkBonusCode();
                } else if (data.result && data.bonus) {
                    currentBonus = data.bonus;
                    checkBonusCode();
                    showBonusTariffList();
                }
              },
              error: function(xhr, status, err) {
                  $('#paidaccessBonuscodeResult .error_ajax .error_msg').text(err);
                  $('#paidaccessBonuscodeResult .error_ajax').show();
                  currentBonus = false;
                  checkBonusCode();
              }
            });
        }
        function isBonusTariff() {
            if (!selectedTariffId || !currentBonus) return false;

            for (var i in currentBonus.tariffs) {
                var t = ''+currentBonus.tariffs[i];
                if (t == 'all') return true;
                if (t.substr(0,1) == 'p') {
                    var selectedTariff = tariffs[selectedTariffId];
                    if (!selectedTariff) return false;
                    if (selectedTariff.plan_id == t.substr(1)) return true;
                }
                if (t == selectedTariffId) return true;
            }
            return false;
        }

        function showBonusTariffList() {
            var list = '';
            var text = '';
            for(var i in currentBonus.tariffs) {
                var t = ''+currentBonus.tariffs[i];
                if (t == 'all') {
                    text = 'Все тарифы';
                    if (tariffs.length == 0) continue;
                    tariffId = tariffs[Object.keys(tariffs)[0]].id;
                } else if (t.substr(0, 1) == 'p') {
                    var planId = t.substr(1);
                    if (!plans[planId]) continue;
                    text = plans[planId].title;
                    if (plans[planId].tariffs.length == 0) continue;
                    tariffId = plans[planId].tariffs[0].id;
                } else if (tariffs[t]) {
                    var planId = tariffs[t].plan_id;
                    if (!plans[planId]) continue;
                    text = plans[planId].title + ' ('+tariffs[t].name+')';
                    tariffId = t;
                } else continue;
                list += '<li><a href="#" class="selectTariff" data-tariffId="'+tariffId+'">'+text+'</a></li>';
            }
            if (list != '') {
                $('#paidaccessBonuscodeResult .invalid_tariff .bonus_tariff_list').html('<ul>'+list+'</ul>');
            }
        }

        function showSubmitBtn(show, price) {
            $('#paidaccessFree').hide();
            $('#paidaccessPay').hide();
            $('#paidaccessActivateBonus').hide();
            if (show == 'bonus') {
                if (price == 0) {
                    $('#paidaccessActivateBonus button').text('Активировать бонус-код и получить бесплатно');
                } else if (price > 0) {
                    $('#paidaccessActivateBonus button').text('Активировать бонус-код и перейти к оплате');
                }
                $('#paidaccessActivateBonus').show();
            } else if (show && price == 0) {
                $('#paidaccessFree').show();
            } else if (show && price > 0) {
                $('#paidaccessPay').show();
            }
        }

        function checkBonusCode() {
            var selectedTariff = false;
            if (selectedTariffId) {
                selectedTariff = tariffs[selectedTariffId];
            }
            if (!currentBonus) {
                $('#paidaccessBonuscodeResult .result_ok').hide();
                $('#paidaccessBonuscodeResult .invalid_tariff').hide();
                showSubmitBtn(selectedTariff, selectedTariff ? selectedTariff.price : 0);
                return;
            }


            var res = isBonusTariff();
            if (res && selectedTariff) {
                var discountText = currentBonus.value;
                var totalPriceWithDiscount = selectedTariff.price;
                if (currentBonus.type == 'discount_percent') {
                    discountText = currentBonus.value + '%';
                    totalPriceWithDiscount *= Math.min(100, Math.max(0, (100 - currentBonus.value))) / 100;
                } else if (currentBonus.type == 'discount_value') {
                    discountText = currentBonus.value;
                    totalPriceWithDiscount = Math.min(totalPriceWithDiscount,
                        Math.max(0, totalPriceWithDiscount - currentBonus.value));
                } else {
                    $('#paidaccessBonuscodeResult .error_not_found').show();
                    return;
                }
                $('#paidaccessBonuscodeResult > div').hide();
                $('#paidaccessBonuscodeResult .result_ok').show();
                $('#paidaccessBonuscodeResult .result_ok .discount_value').text(discountText);
                $('#paidaccessBonuscodeResult .result_ok .price_with_discount').text(totalPriceWithDiscount);
                showSubmitBtn('bonus', totalPriceWithDiscount);

            } else {
                $('#paidaccessBonuscodeResult > div').hide();
                $('#paidaccessBonuscodeResult .invalid_tariff').show();
                showSubmitBtn(selectedTariff, selectedTariff ? selectedTariff.price : 0);
            }

        }

        $('#txtPaidaccessBonuscode').on('change keyup click', queueCheckBonuscode);
        $(document).on('click', '.selectTariff',selectTariffClick);

        $('.paidaccessTarifRadioBtn').click(function() {
            setSelectedTariffId($(this).val());
        });
    });
</script>