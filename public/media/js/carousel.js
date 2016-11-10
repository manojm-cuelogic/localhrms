;
(function($) {
    var Carousel = function(poster) {
        var self = this;
        //保存单个旋转木马对象
        this.poster = poster;
        this.posterItemMain = poster.find("ul.poster-list");
        this.nextBtn = poster.find("div.poster-next-btn");
        this.prevBtn = poster.find("div.poster-prev-btn");
        this.posterItems = poster.find("li.poster-item");
        if (this.posterItems.size() % 2 == 0) {
            this.posterItemMain.append(this.posterItems.eq(0).clone());
            this.posterItems = this.posterItemMain.children();
        };
        this.posterFirstItem = this.posterItems.first();
        this.posterLastItem = this.posterItems.last();
        this.rotateFlag = true;
        //默认配置参数
        var width = $('.carousal').width();
        var height = $('.carousal').height();
        this.setting = {
            "width": width, //幻灯片的宽度
            "height": 370, //幻灯片的高度
            "posterWidth": 640, //幻灯片第一帧的宽度
            "posterHeight": 270, //幻灯片第一帧的高度
            "scale": 0.9, //记录显示比例关系
            "speed": 500,
            "autoPlay": false,
            "delay": 5000,
            "verticalAlign": "middle" //top bottom
        };
        $.extend(this.setting, this.getSetting());

        //$('.poster-main').css('height: 600');

        //设置配置参数值
        this.setSettingValue();
        //初始化幻灯片位置
        this.setPosterPos();
        //左旋转按钮
        this.nextBtn.click(function() {
            if (self.rotateFlag) {
                self.rotateFlag = false;
                self.carouseRotate("left");
            };
        });
        //右旋转按钮
        this.prevBtn.click(function() {
            if (self.rotateFlag) {
                self.rotateFlag = false;
                self.carouseRotate("right");
            };
        });
        //是否开启自动播放
        if (this.setting.autoPlay) {
            this.autoPlay();
            this.poster.hover(function() {
                //self.timer是setInterval的种子
                window.clearInterval(self.timer);
            }, function() {
                self.autoPlay();
            });
        };
    };
    Carousel.prototype = {

        autoPlay: function() {
            var self = this;
            this.timer = window.setInterval(function() {
                self.nextBtn.click();
            }, this.setting.delay);
        },

        //旋转
        carouseRotate: function(dir) {

            var _this_ = this;
            var zIndexArr = [];
            //左旋转
            if (dir === "left") {
                var temp = false,
                    count = this.posterItems.length-1,
                    toShowIndex = -1,
                    prev = null;

                this.posterItems.each(function(i,v) {

                    var self = $(this),
                        prev = self.prev().get(0) ? self.prev() : _this_.posterLastItem,
                        width = prev.width(),
                        height = prev.height(),
                        opacity = prev.css("opacity"),
                        left = prev.css("left"),
                        top = prev.css("top"),
                        zIndex = prev.css("zIndex");

                    if (width == 300) {
                        $(self).addClass('active');
                        $('.carousal-description').addClass('hide');
                        jQuery("#desc_" + $(self).attr('id')).removeClass('hide');
                        console.log($( "li.poster-item" ).index( self ) + " ====== " + count);
                        if($( "li.poster-item" ).index( self ) == count){
                            $('li.poster-item:first').css('display','block')
                        } else {
                            $(self.next('li.poster-item')).css('display','block');
                        }
                        if($( "li.poster-item" ).index( self ) == 0){
                            $('li.poster-item:last').css('display','block')
                            toShowIndex = count;
                        } else {
                            $(self.prev('li.poster-item')).css('display','block');
                            toShowIndex = -1;
                        }
                        $(self).css('display','block'); 
                        temp=true;
                        $(prev).css('display','block');  

                    } else if(!temp) {
                        $(self).removeClass('active');
                        if(toShowIndex != $( "li.poster-item" ).index( self ))
                            $(self).css('display','none');  
                    } else {
                        temp=false;
                        $(self).removeClass('active');
                    }

                    zIndexArr.push(zIndex);
                    self.animate({
                        width: width,
                        height: height,
                        //zIndex :zIndex,
                        opacity: opacity,
                        left: left,
                        top: top
                    }, _this_.setting.speed, function() {
                        _this_.rotateFlag = true;
                    });
                });
                //zIndex需要单独保存再设置，防止循环时候设置再取的时候值永远是最后一个的zindex
                this.posterItems.each(function(i) {
                    $(this).css("zIndex", zIndexArr[i]);
                });
            } else if (dir === "right") { //右旋转
                var temp = false,
                    count = this.posterItems.length-1,
                    toShowIndex = -1,
                    prev = null;

                this.posterItems.each(function() {

                    var self = $(this),
                        next = self.next().get(0) ? self.next() : _this_.posterFirstItem,
                        width = next.width(),
                        height = next.height(),
                        opacity = next.css("opacity"),
                        left = next.css("left"),
                        top = next.css("top"),
                        zIndex = next.css("zIndex");

                    if (width == 300) {
                        $('.carousal-description').addClass('hide');
                        jQuery("#desc_" + $(self).attr('id')).removeClass('hide');
                        $(self).addClass('active');


                        if($( "li.poster-item" ).index( self ) == count){
                            $('li.poster-item:first').css('display','block')
                        } else {
                            $(self.next('li.poster-item')).css('display','block');
                        }
                        if($( "li.poster-item" ).index( self ) == 0){
                            $('li.poster-item:last').css('display','block')
                            toShowIndex = count;
                        } else {
                            $(self.prev('li.poster-item')).css('display','block');
                            toShowIndex = -1;
                        }
                        $(self).css('display','block'); 
                        temp=true;
                        $(prev).css('display','block');  

                    } else if(!temp) {
                     
                        $(self).removeClass('active');
                        if(toShowIndex != $( "li.poster-item" ).index( self ))
                            $(self).css('display','none');  

                    } else {
                        temp=false;
                         $(self).removeClass('active');
                    }

                    zIndexArr.push(zIndex);
                    self.animate({
                        width: width,
                        height: height,
                        //zIndex :zIndex,
                        opacity: opacity,
                        left: left,
                        top: top
                    }, _this_.setting.speed, function() {
                        _this_.rotateFlag = true;
                    });
                });
                //zIndex需要单独保存再设置，防止循环时候设置再取的时候值永远是最后一个的zindex
                this.posterItems.each(function(i) {
                    $(this).css("zIndex", zIndexArr[i]);
                });
            };
        },
        //设置剩余的帧的位置关系
        setPosterPos: function() {
            var self = this,
                sliceItems = this.posterItems.slice(1),
                sliceSize = sliceItems.size() / 2,
                rightSlice = sliceItems.slice(0, sliceSize),
                //存在图片奇偶数问题
                level = Math.floor(this.posterItems.size() / 2),
                leftSlice = sliceItems.slice(sliceSize);

            //设置右边帧的位置关系和宽度高度top
            var firstLeft = (this.setting.width - this.setting.posterWidth) / 2;
            var rw = this.setting.posterWidth,
                fixOffsetLeft = firstLeft + rw,
                rh = this.setting.posterHeight,
                gap = ((this.setting.width - this.setting.posterWidth) / 2) / level;

            //设置右边位置关系
            rightSlice.each(function(i) {
                level--;
                rw = rw * self.setting.scale;
                rh = rh * self.setting.scale;
                var j = i;
                $(this).css({
                    zIndex: level,
                    width: rw,
                    height: rh,
                    opacity: 0.5,
                    left: fixOffsetLeft + (++i) * gap - rw + 35,
                    top: 100
                });
            });

                $(rightSlice[0]).css('display','block');

            //设置左边的位置关系
            var lw = rightSlice.last().width(),
                lh = rightSlice.last().height(),
                oloop = Math.floor(this.posterItems.size() / 2);
            leftSlice.each(function(i) {
                $(this).css({
                    zIndex: i,
                    width: lw,
                    height: lh,
                    opacity: 0.5,
                    left: i * gap + -40,
                    top: 100
                });

                $(leftSlice[leftSlice.length-1]).css('display','block');

                lw = lw / self.setting.scale;
                lh = lh / self.setting.scale;
                oloop--;
            });
        },

        //设置垂直排列对齐
        setVerticalAlign: function(height) {
            var verticalType = this.setting.verticalAlign,
                top = 0;
            if (verticalType === "middle") {
                top = (this.setting.height - height) / 2;
            } else if (verticalType === "top") {
                top = 0;
            } else if (verticalType === "bottom") {
                top = this.setting.height - height;
            } else {
                top = (this.setting.height - height) / 2;
            };
            return top;
        },

        //设置配置参数值去控制基本的宽度高度。。。
        setSettingValue: function() {
            this.poster.css({
                width: this.setting.width,
                height: this.setting.height
            });
            this.posterItemMain.css({
                width: this.setting.width - 90,
                height: this.setting.height
            });
            //计算上下切换按钮的宽度
            var w = (this.setting.width - this.setting.posterWidth) / 2;
            //设置切换按钮的宽高，层级关系
            this.nextBtn.css({
                
                zIndex: Math.ceil(this.posterItems.size() / 2)
            });
            this.prevBtn.css({
                
                zIndex: Math.ceil(this.posterItems.size() / 2)
            });
            this.posterFirstItem.css({
                width: this.setting.posterWidth,
                height: this.setting.posterHeight,
                left: w,
                top: 0,
                display:'block',
                zIndex: Math.floor(this.posterItems.size() / 2)
            }, $(this.posterFirstItem).addClass('active'));
        },

        //获取人工配置参数
        getSetting: function() {
            var setting = this.poster.attr("data-setting");
            if (setting && setting != "") {
                return $.parseJSON(setting);
            } else {
                return {};
            };
        }
    };

    Carousel.init = function(posters) {
        var _this_ = this;
        posters.each(function() {
            new _this_($(this));
        });
    };

    //挂载到window下
    window["Carousel"] = Carousel;

})(jQuery);
