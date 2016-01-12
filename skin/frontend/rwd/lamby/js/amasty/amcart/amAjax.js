AmAjax = Class.create();
AmAjax.prototype =
{
    options : null,

    nimiCartClass : 'a.top-link-cart',

    url : null,

    updateUrl : null,

    srcImageProgress : null,

    isProductView : 0,

    typeLoading : 0,

    enMinicart : 0,

    productId : 0,

    initialize : function(options) {
        this.url = options['send_url'];
        this.updateUrl = options['update_url'];
        this.enMinicart = options['enable_minicart'];
        this.typeLoading = options['type_loading'];
        this.options = options;
        this.srcImageProgress = options['src_image_progress'];
        this.isProductView = options['is_product_view'];
        this.productId = options['product_id'];
    },

    getPosition: function(box) {
        var y = box.offsetHeight;
        var x = 0;
        while (box && box.tagName != 'BODY')
        {
            y = y + box.offsetTop;
            x = x + box.offsetLeft;
            box = box.offsetParent;
        }
        return {top:y, left:x};
    },


    updateCart : function() {
         $$('.block-cart').each(function(element) {
               var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'cart');//    replace ajax to cart
               new Ajax.Updater(element, url, {
                   method: 'post'
               });
               return true;
         }.bind(this));
    },

    updateLinc : function(count) {
         var element = $$(this.nimiCartClass)[0];
         if(element) {
              var pos = element.innerHTML.indexOf("(");
              if(pos > 0 && count) {
                  $j('#cart-total').text(count);
              }
              else{
                  if(count)
                    $j('#cart-total').text(count);
              }
              new Effect.Morph(element, {
                  style: 'color: #ffff00;font-weight:bold;',
                  duration: 0.8,
                  afterFinish: function() {
                       new Effect.Morph(element, {
                          style: 'color: #EBBC58;font-weight:normal;',
                          duration: 0.4
                      });
                  }
              });
              element.title = element.innerHTML
         };
    },

    showAnimation: function(loading, element) {
        var foundImage = 0;
        jQuery(function($) {
            var progress = document.createElement('div');
            progress = $(progress); // fix for IE
            progress.attr('id','amprogress');

            var container = document.createElement('div');
            container = $(container); // fix for IE
            container.attr('id','amimg_container');
            container.appendTo(progress);

            var img = document.createElement('img');
            img = $(img); // fix for IE
            img.attr('src', this.srcImageProgress);
            img.appendTo(container);

            container.width('150px');
            var width = container.width();
            width = "-" + width/2 + "px" ;
            container.css("margin-left", width);
            progress.hide().appendTo('body').fadeIn();
        }.bind(this));
    },

    hideAnimation: function() {
        if($('amprogress')) {
            jQuery(function($) {
                $('#amprogress').fadeOut(function() {
                    $(this).remove();
                });
            });
        }
    },

   //run every second while time !=0
   oneSec: function() {
        jQuery(function($) {
            var elem= $('#confirmButtons .button:last-child');
            var value = elem.text();
            var sec = parseInt(value.replace(/\D+/g,""));
            if(sec) {
                value =  value.replace(sec, sec-1);
                elem.text(value);
                if(sec <= 1) {
                    clearInterval(document.timer);
                    elem.click();
                }
            }
            else{
                 clearInterval(document.timer);
            }

        });
    },

    //add parametr from form on product view page
    addProductParam: function(postData) {
        var form = $('product_addtocart_form');
        if(form) {
            var len=form.elements.length-1;
            var tmpPostData = postData;
            var validator = new Validation(form);
            if (validator.validate()) {
                for(var i=0; i<=len; i++) {
                    var element = form.elements[i];
                    if ((element.value != 0 && element.value != undefined && element.value != null)) {
                        if(((element.type == "checkbox" || element.type == "radio") && element.checked) || !(element.type == "checkbox" || element.type == "radio"))
                            postData += '&' + element.name + '=' + element.value;
                    }
                }
            }
            else{
                 return '';
            }
        }
        else{
            form = $('product_addtocart_form-' + postData.replace(/[^\d]/gi, ''));
            if(form && $('amconf-amcart-' + postData.replace(/[^\d]/gi, ''))) {
                  if (form.hasClassName('isValid')) {
                    var len=form.elements.length-1;
                    for(var i=0; i<=len; i++) {
                        var element = form.elements[i];
                        if ((element.value != 0 && element.value != undefined && element.value != null) ) {
                            if(((element.type == "checkbox" || element.type == "radio") && element.checked) || !(element.type == "checkbox" || element.type == "radio"))
                                postData += '&' + element.name + '=' + element.value;
                        }
                    }
                    form.remove();
                  }
                  else{
                     // formValidation(form);
                      form.remove();
                      //return '';
                  }
            }
        }
        postData += '&IsProductView=' + this.isProductView;
        return postData;
    },

    sendAjax : function(idProduct, param, oldEvent, element) {
        if(idProduct) {
            postData = 'product_id='+idProduct;
            postData = this.addProductParam( postData );
            if('' == postData)
                return true;
            if(param) {
                jQuery(function($) {
                    $.confirm.hide();
                })
            }
            new Ajax.Request(this.url, {
                method: 'post',
                postBody : postData,
                onCreate: function()
                {
                   this.showAnimation(this.typeLoading, element);
                }.bind(this),
                onComplete: function()
                {
                   this.hideAnimation();
                }.bind(this),
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            this.hideAnimation();
                            alert(response.error);
                        }
                        else{
                             if(response.redirect) {
                                 //if IE7
                                if (document.all && !document.querySelector) {
                                   oldEvent = oldEvent.substring(21, oldEvent.length-2)
                                   eval(oldEvent);
                                }
                                else{
                                    eval(oldEvent);
                                }
                                return true;
                             }
                             this.hideAnimation();
                             var act1 = response.b1_action;
                             var act2 = response.b2_action;
                             jQuery(function($) {
                                 $.confirm({
                                    'title'      : response.title,
                                    'message'    : response.message,
                                    'buttons'    : {
                                        '1'    : {
                                            'name'  :  response.b1_name,
                                            'class'    : 'blue',
                                            'action': function() {
                                                eval(act1);
                                            }
                                        },
                                        '2'    : {
                                            'name'  :  response.b2_name,
                                            'class'    : 'gray',
                                            'action': function() {
                                                eval(act2);
                                            }
                                        }
                                    }
                                 });

                             }.bind(this));

                              var maxHeight = parseInt($$('html')[0].getHeight()/4);
                              var maxHeightRel = parseInt($$('html')[0].getHeight()/2.5);
                              var height = document.getElementById('messageBox').getHeight();
                              if(!(height <= maxHeight || (height <= maxHeightRel && $('am-block-related')) )) {
                                      $('messageBox').setStyle({
                                          overflowY : 'scroll',
                                          maxHeight : maxHeight + 'px'
                                      });
                              }
                             try {
                                 eval(response.script);
                             } catch(e) {
                                console.debug(e);
                             }
                             this.updateCart();
                             this.updateMinicart();
                             this.updateLinc(response.count);
                         }
                    }
                }.bind(this),
                onFailure: function()
                {
                    this.hideAnimation();
                    eval(oldEvent);
                }.bind(this)
            });
        }
    },


    //minicart
    createMinicart: function() {
        var nmCart = $$(this.nimiCartClass)[0];
        if(nmCart) {
            var container = document.createElement('div');
            container = $(container);
            container.id = 'am_minicart_container';
            container.style.display = 'none';
            if(nmCart.parentNode){
                nmCart.parentNode.appendChild(container);
                this.updateMinicart();

                Event.observe(container, 'mouseover',function() {AmAjaxObj.showMinicart()} );
                Event.observe(container, 'mouseout',function() {AmAjaxObj.hideMinicart()} );
                Event.observe(nmCart,   'mouseover',function() {AmAjaxObj.showMinicart()} );
                Event.observe(nmCart,   'mouseout',function() {AmAjaxObj.hideMinicart()} );
            }
            return;
        }
    },

    updateMinicart: function() {
               var url = AmAjaxObj.url.replace(AmAjaxObj.url.substring(AmAjaxObj.url.length-6, AmAjaxObj.url.length), 'minicart');
               var element = $('am_minicart_container');
               new Ajax.Updater(element, url, {
                   method: 'post'
               });
    },

    showMinicart: function() {
         jQuery(function($) {
            $("#am_minicart_container").stop(true, true).delay(300).slideDown(500, "easeOutBounce");
         });
    },



    hideMinicart: function() {
         jQuery(function($) {
            $("#am_minicart_container").stop(true, true).delay(300).fadeOut(800, "easeInCubic");
         });
    },

    searchInPriceBox: function(parent, oldEvent, element, idProduct) {
        var child  = parent.getElementsByClassName('price-box')[0];
        if(child) {
            var childNext = child.childElements()[0];
            if(childNext){
              idProduct = childNext.id.replace(/[^\d]/gi, '');
            }

            if(!idProduct && idProduct != '') {
                child.childElements()[0].childElements().each(function(childNext) {
                    idProduct = childNext.id.replace(/[a-z-]*/, '');
                    if(parseInt(idProduct) > 0) {
                        var tmp = parseInt(idProduct);
                        this.sendAjax(tmp, '', oldEvent, element);
                        return idProduct;
                    }
                });
            }
            if(parseInt(idProduct) > 0) {
                 var tmp = parseInt(idProduct);
                 this.sendAjax(tmp, '', oldEvent, element);
                 return idProduct;
            }
            else {
                idProduct = '';
            }
        }
        return '';
    }
}


//Class for increasing product count

AmQty = Class.create();
AmQty.prototype =
{
    initialize : function(min) {
        this.min = min;
        this.input = $('am-input');
    },

    increment: function() {
        this.input.value++;
        this.paint();
    },

    decrement: function() {
         if(this.input.value > this.min) {
             this.input.value--;
             this.paint();
         }

    },

    update: function() {
            postData = "update_cart_action=update_qty&" + this.input.name + '=' + this.input.value;
            new Ajax.Request(AmAjaxObj.updateUrl, {
                method: 'post',
                postBody : postData,
                onCreate: function()
                {
                   AmAjaxObj.showAnimation();
                }.bind(this),

                onComplete: function()
                {
                  AmAjaxObj.hideAnimation();
                }.bind(this),

                onSuccess: function(transport) {
                      if($('amcart-count')) {
                          var url = AmAjaxObj.url.replace(AmAjaxObj.url.substring(AmAjaxObj.url.length-6, AmAjaxObj.url.length), 'count');//    replace ajax to count
                          new Ajax.Updater($('amcart-count'), url, {
                               method: 'post',
                               onComplete: function() {
                                   AmAjaxObj.updateLinc(" (" + $$('#amcart-count a')[0].text + ")");
                               }
                          });

                      }
                      AmAjaxObj.updateCart();
                      AmAjaxObj.hideAnimation();
                      new Effect.Highlight(this.input, { startcolor: '#ffff99', endcolor: '#a4e9ac', restorecolor : '#a4e9ac'});
                      $('am-qty-button-update').hide();
                      this.input.removeClassName('focus');

                }.bind(this),

                onFailure: function()
                {
                    AmAjaxObj.hideAnimation();
                }.bind(this)
            });
    },

    paint: function() {
         new Effect.Highlight('am-input', { endcolor: '#ffff99', restorecolor : '#ffff99'});
         $('am-input').addClassName('focus');
         $('am-qty-button-update').show();
         this.clearTimer();
    },

    clearTimer: function() {
        jQuery(function($) {
            var elem= $('#confirmButtons .button:last-child');
            var value = elem.text();
            var sec = parseInt(value.replace(/\D+/g,""));
            if(sec) {
                value =  value.replace('(' + sec + ')', '');
                elem.text(value);
                clearInterval(document.timer);
            }
        });
    }
}


function searchIdAndSendAjax(event) {
    var element = Event.element(event);
    event.preventDefault();
    var addToLinc = 'add-to-links';

    if($('confirmBox')) {
        jQuery(function($) {
            $.confirm.hide();
        })
    }
    //in Chrome element = span
    if(!element.hasClassName('button')) {
         element = $(element.parentNode.parentNode);
    }

    //if colors swatches pro
    if(amconf = element.getAttribute('amconf')) {
        eval(amconf);
    }

    var oldEvent = element.getAttribute('oldEvent');
    var idProduct = '';

    //category page
    var el = $(element.parentNode.parentNode);
    if(el) {
        var idProduct = AmAjaxObj.searchInPriceBox(el, oldEvent, element, idProduct);
    }
    //product page
    if(idProduct == '') {
        var el = $(element.parentNode.parentNode.parentNode);
        if(el) {
            var idProduct = AmAjaxObj.searchInPriceBox(el, oldEvent, element, idProduct);
        }
    }
    //if related products on product page
    if(idProduct == '') {
        var el = $(element.parentNode);
        if(el) {
             var idProduct = AmAjaxObj.searchInPriceBox(el, oldEvent, element, idProduct);
        }
    }
    //for bundle
    if(idProduct == '') {
        var el = $(element.parentNode);
        var child  = el.getElementsByClassName(addToLinc)[0];
        if(child) {
            var childNext = child.childElements()[0];
            if(childNext) {
                var childNext = childNext.childElements()[0];
            }
            if(childNext) {
                var idProduct = childNext.href.match(/product(.?)+/)[0].replace(/[^\d]/gi, '');
            }
            if(parseInt(idProduct) > 0) {
                 var tmp = parseInt(idProduct);
                 AmAjaxObj.sendAjax(tmp, '', oldEvent, element);
                 return true;
            }
             else{
                idProduct = '';
            }
        }
    }
    //other
    if(idProduct == '' && oldEvent) {
        var productString = '/product/';
        var posStart = oldEvent.indexOf(productString);
        if(posStart) {
            var posFinish = oldEvent.indexOf('/', posStart + productString.length);
            if(posFinish) {
                var idProduct = oldEvent.substring(posStart + productString.length, posFinish);
                   if(parseInt(idProduct) > 0) {
                         var tmp = parseInt(idProduct);
                         AmAjaxObj.sendAjax(tmp, '', oldEvent, element);
                         return true;
                   }
                else {
                    idProduct = '';
                }
            }
        }
    }
   //default acrion
    if(idProduct == '') {
         //if IE7
        if (document.all && !document.querySelector) {
           oldEvent = oldEvent.substring(21, oldEvent.length-2)
        }
        eval(oldEvent);
    }
}


function AmAjaxShoppCartLoad(buttonClass){
    $$(buttonClass).each(function(element){
        if(!element.hasClassName('amcart-ignore')){
            if(element.getAttribute('onclick')){
                var attr = document.createAttribute('oldEvent');
                attr.nodeValue =  element.getAttribute('onclick').toString();
                element.attributes.setNamedItem(attr);
            }
            element.onclick = '';
            Event.observe(element, 'click', searchIdAndSendAjax );
        }
    }.bind(this));
    if(AmAjaxObj.enMinicart === "1"){
        AmAjaxObj.createMinicart()
    }
}

Event.observe(window, 'load', function(){
    AmAjaxShoppCartLoad('button.btn-cart');
});

