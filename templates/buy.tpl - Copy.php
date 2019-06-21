<?php
use pdima88\phpassets\Assets;
/** @var cmsTemplate $this */
    $pageTitle = 'Приобрести подписку';
    $this->setPageTitle($pageTitle);
    Assets::add('vue');
    Assets::add('ion.rangeSlider');
    $this->addHead(Assets::getCss());
    echo Assets::getJs();
    $this->addControllerCSS('paidaccess');
?>
<h1><?= $pageTitle ?></h1>

<div id="paidaccessBuy">
<form>
  <input type="hidden" name="plan_id">
  <input type="hidden" name="tariff_id">

</form>

<div id="paidaccessSelectTariffPlan">
<h2 class="paidaccess-select-plan">Выберите тарифный план:</h2>

<div class="paidaccess-plans">
    <plan v-for="plan in plans" :key="plan.id"
          :plan="plan" @select-plan="selectPlan"
          :is-selected="selectedPlan == plan">

    </plan>
</div>

</div>

<div id="paidaccessSelectTariff" v-show="selectedPlan">
<h2 class="paidaccess-select-tariff">Выберите срок:</h2>
    <div class="paidaccess-tariffs">

        <input type="text" id="tariffSelectRangeSlider" name="tariff_id"/>
    </div>
</div>

    <div id="paidaccessSelectedTariffInfo" v-if="selectedTariff">
        Стоимость подписки за {{ selectedTariff.name }} {{ selectedTariff.price }} <br>
        Кол-во вопросов, которые вы можете задать экспертам: {{ selectedTariff.bonus }}




    </div>

    <div id="paidaccessUseBonuscode" v-if="selectedTariff">
        <h2 class="paidaccess-use-bonuscode">Использовать бонус-код:</h2>


    </div>


</div>

<script>
    $(function()
    {
        Vue.config.devtools = true;
        Vue.component('plan', {
            props: ['plan', 'isSelected'],
            template: '<div class="paidaccess-plan" :class="{ selected: isSelected }" ' +
                '@click="$emit(\'select-plan\', plan)" >' +
                '<h3 class="paidaccess-plan-title">{{ plan.title }}</h3>' +
                '<div class="paidaccess-plan-description" v-html="plan.hint"></div>'+
                '</div>'
        });

        var paidaccessBuy = new Vue({
            el: '#paidaccessBuy',
            data: {
                selectedPlan: null,
                selectedTariff: false,
                planTariffs: null,
                plans: <?= json_encode($plans) ?: '[]' ?>,
                tariffs: <?= json_encode($tariffs) ?: '[]' ?>,
            },
            /*computed: {
                selectedTariff: function() {
                    if (this.selectedTariffId !== false && this.selectedTariffId !== null &&
                        this.tariffs[this.selectedTariffId] != undefined ) {
                        return this.tariffs[this.selectedTariffId];
                    }
                    return null;
                }
            },*/
            methods: {
                selectPlan: function(plan) {
                    this.selectedPlan = plan;
                    this.planTariffs = plan.tariffs;
                    var tariffArr = [];
                    for (var p in plan.tariffs) {
                        tariffArr.push(plan.tariffs[p].name);
                    }
                    initIonRangeSlider(tariffArr);
                    tariffSelectRangeSlider.update({
                        from: this.selectedPlan.tariffs.length - 1
                    });
                }
            }
        });

        var sliderChange = function (data) {
            var i = data.from;
            if (paidaccessBuy.selectedPlan && paidaccessBuy.selectedPlan.tariffs[i] != undefined) {
                paidaccessBuy.selectedTariff = paidaccessBuy.selectedPlan.tariffs[i];
            }
        }

        var tariffSelectRangeSlider = null;
        function initIonRangeSlider(data) {
            if (tariffSelectRangeSlider != null) {
                tariffSelectRangeSlider.destroy();
            }
            tariffSelectRangeSlider = $('#tariffSelectRangeSlider').ionRangeSlider({
                skin: 'big',
                grid: true,
                values: data,
                onChange: sliderChange,
                onUpdate: sliderChange
            }).data("ionRangeSlider");

        }

    });
</script>