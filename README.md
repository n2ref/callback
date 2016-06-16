# Callback
Tool for receiving requests for calls from your site

![preview](https://raw.githubusercontent.com/shabuninil/callback/master/preview.png) 

```html
<div id="callback-widget"
     class="callback-widget-yellow-black callback-widget-right callback-widget-bottom">
    <div id="callback-widget-container">
        <div id="callback-widget-btn" onclick="$('#callback-modal').modal('show')"></div>
        <div id="callback-widget-icon"></div>
        <div id="callback-widget-animation"></div>
    </div>
    <div id="callback-widget-text">Перезвоним вам ;)</div>
</div>

<div class="modal fade" id="callback-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="text-center">
                    <h1>Остались вопросы?</h1>
                    <p>Оставьте ваш номер и мы перезвоним вам прямо сейчас!</p>
                </div>
                <form class="form-horizontal" style="padding:10px"
                      onsubmit="callback.order(this); return false;">
                    <div class="form-group">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                            <div class="col-xs-7 col-sm-7">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></div>
                                    <input type="tel" class="form-control" id="inputPhone"
                                           placeholder="+37529" name="phone" required>
                                </div>
                            </div>
                            <div class="col-xs-5 col-sm-5">
                                <input type="submit" value="Заказать звонок" class="btn btn-success callback-loading">
                            </div>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="clearfix"></div>
                        <div id="callback-response"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>```