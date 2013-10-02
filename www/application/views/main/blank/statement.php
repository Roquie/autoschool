<?=HTML::script('js/statement.js')?>
<?=HTML::style('css/statement.css')?>
<?=HTML::script('js/vendor/chosen.js')?>
<?=HTML::style('css/vendor/chosen.css')?>
<div class="container" style="margin-top: 35px">
    <div id="result">
        <h2 class="info">
            Чтобы оставить заявку на поступление в автошколу, вам нужно просто ввести необходимую информацию в соответствующие поля. Всё остальное мы сделаем за вас. <br>
            В результате вы получите составленное заявление и договор на основе введённых данных.
        </h2>
        <br>
            <div class="well well-small form-block">

                <ul id="tabs" class="nav nav-tabs">
                    <li class="active"><a href="#tab1">Заявление</a></li>
                    <li><a href="#tab2">Договор</a></li>
                </ul>

                <div class="tab-pane" id="tab1">
                    <form style="margin-bottom: 50px" id="statement" action="#" method="post">
                        <div class="row-fluid">

                            <div class="span6 pull-left">

                                <label for="famil">Фамилия</label>
                                <input type="text" id="famil" autofocus="autofocus" name="famil" data-placement="top" data-req="true" tabindex="1">

                                <label for="imya">Имя</label>
                                <input type="text" name="imya" id="imya" data-req="true" tabindex="2">

                                <label for="ot4estvo">Отчество</label>
                                <input type="text" name="ot4estvo" id="ot4estvo" tabindex="3">

                                <label for="data_rojdeniya">Дата рождения</label>
                                <div class="input-append input-block-level">
                                    <input type="text" id="data_rojdeniya" name="data_rojdeniya" data-req="true" tabindex="4"/>
                                    <span class="add-on btn" id="calendar"><i class="icon-calendar"></i></span>
                                </div>

                                <label for="grajdanstvo">Гражданство</label>
                                <select id="grajdanstvo" name="grajdanstvo" style="width: 453px" data-req="true" tabindex="5">
                                    <option>РФ</option>
                                    <option>Украина</option>
                                    <option>Белоруссия</option>
                                </select>

                                <label for="mesto_rojdeniya" style="margin-top: 12px">Место рождения</label>
                                <input type="text" name="mesto_rojdeniya" id="mesto_rojdeniya" data-req="true" tabindex="6">

                                <label for="adres_reg_po_pasporty">Адрес регистрации по паспорту</label>
                                <input type="text" name="adres_reg_po_pasporty" id="adres_reg_po_pasporty" data-req="true" tabindex="7">

                                <div class="check" id="toggleReg">
                                    <span class="label-check">Временная регистрация?</span>
                                    <input type="checkbox" name="toggleReg">
                                </div>

                                <div id="reg" style="display: none">
                                    <label for="vrem_reg">Временная регистрация</label>
                                    <input type="text" name="vrem_reg" id="vrem_reg" tabindex="7">
                                </div>


                            </div>

                            <div class="span6 pull-right">

                                <label>Паспорт</label>
                                <input class="span6" type="text" name="pasport_seriya" id="seriya" data-value="серия" data-req="true" tabindex="8">
                                <input class="span6 pull-right" type="text" name="pasport_nomer" id="nomer" data-value="номер" data-req="true" tabindex="9">

                                <label for="pasport_data_vyda4i">Дата выдачи паспорта</label>
                                <div class="input-append input-block-level">
                                    <input type="text" id="pasport_data_vyda4i" name="pasport_data_vyda4i" data-req="true" tabindex="10">
                                    <span class="add-on btn" id="calendar"><i class="icon-calendar"></i></span>
                                </div>

                                <label for="from">Кем выдан паспорт</label>
                                <input type="text" id="from" name="pasport_kem_vydan" data-req="true" tabindex="11">

                                <label>Телефон</label>
                                <input class="span6" type="text" id="telephone_m" name="mob_tel" data-value="мобильный" data-req="true"  tabindex="12">
                                <input class="span6 pull-right" type="text" id="telephone_d" name="dom_tel" data-value="домашний" tabindex="13">

                                <label for="obrazovanie">Образование</label>
                                <input type="text" name="obrazovanie" id="obrazovanie" data-req="true" tabindex="14">

                                <label for="mesto_raboty">Место работы</label>
                                <input type="text" name="mesto_raboty" id="mesto_raboty" data-req="true" tabindex="15">

                                <div class="select">
                                    <a href="javascript:void(0);" class="slct">Как Вы узнали о нас</a>
                                    <ul class="drop">
                                        <li>Интернет</li>
                                        <li>Знакомые</li>
                                        <li>Листовка у метро</li>
                                        <li id="other">Другое</li>
                                    </ul>
                                    <input type="hidden" id="select" name="about" />
                                </div>

                                <div style="display: none" id="otherText">
                                    <label for="about_us">Укажите, как Вы узнали о нас</label>
                                    <textarea id="about_us"></textarea>
                                </div>

                            </div>

                        </div>
                        <button class="btn btn-info pull-right" id="next">Далее</button>
                    </form>
                </div>

                <div class="tab-pane" id="tab2">
                    <form style="margin-bottom: 50px" id="contract" action="#" method="post">
                        <div class="row-fluid">
                            <div class="span12">

                                <div class="check active" id="customer">
                                    <span class="label-check">Заказчиком будет родитель (опекун)</span>
                                    <input type="checkbox" name="parent" checked>
                                </div>

                                <legend>Заказчик</legend>

                                <div class="row-fluid">

                                    <div class="span6 pull-left">

                                        <label for="familCustomer">Фамилия</label>
                                        <input type="text" id="familCustomer" name="familiyaCustomer" tabindex="16">

                                        <label for="imyaCustomer">Имя</label>
                                        <input type="text" name="imyaCustomer" id="imyaCustomer" tabindex="17">

                                        <label for="ot4estvoCustomer">Отчество</label>
                                        <input type="text" name="ot4estvoCustomer" id="ot4estvoCustomer" tabindex="18">

                                        <label for="adres_reg_po_pasportyCustomer">Адрес места жительства</label>
                                        <input type="text" name="adres_reg_po_pasportyCustomer" id="adres_reg_po_pasportyCustomer" tabindex="19">

                                    </div>

                                    <div class="span6 pull-right">

                                        <label>Паспорт</label>
                                        <input class="span6" type="text" name="pasport_seriyaCustomer" id="pasport_seriyaCustomer" data-value="серия" tabindex="20">
                                        <input class="span6 pull-right" type="text" name="pasport_nomerCustomer" id="pasport_nomerCustomer" data-value="номер" tabindex="21">

                                        <label for="fromCustomer">Кем выдан паспорт</label>
                                        <input type="text" id="fromCustomer" name="pasport_kem_vydanCustomer" tabindex="22">

                                        <label for="phoneCustomer">Телефон</label>
                                        <input class="span6" type="text" id="phoneCustomer" name="phoneCustomer" tabindex="23">

                                    </div>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="line"></div>

                            <input type="button" class="btn btn-success span12 send" id="send" value="Отправить заявку" data-url="<?=URL::site('lk/ajax/addPapers')?>">
                        </div>
                    </form>
                </div>

            </div>
    </div>
</div>