(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function($) {
    // Extends plugins for adding hello.
    //  - plugin is external module for customizing.
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'emoji': function(context) {
            var self = this;

            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;

            // Don't close when clicking in search input
            var addListener = function () {
                $('body').on('click', '.note-ext-emoji-search', function (e) {
                    e.stopPropagation();
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-smile', function (e) {
                    self.filter("smile");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-hand', function (e) {
                    self.filter("hand");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-people', function (e) {
                    self.filter("people");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-nature', function (e) {
                    self.filter("nature");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-object', function (e) {
                    self.filter("object");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-place', function (e) {
                    self.filter("place");
                });
                $('body').on('click', '.note-ext-ssm-emoji-list-symbol', function (e) {
                    self.filter("symbol");
                });
            };

            // add hello button
            context.memo('button.emoji', function() {

                var buttonGroup = ui.buttonGroup({
                    children:[
                        ui.button({
                            contents: '<i class="ssm-emoji-btn-gg-smile"/>',
                            data: {
                                toggle: 'dropdown'
                            },
                            click: function() {
                                if($(window).width() < 1024){
                                    var obj = $('.ssm-emoji-dropdown');
                                    var off = obj.parent().offset();
                                    if(off.left > obj.width()) {
                                        var nLeft = -1 * obj.width();
                                        $('.ssm-emoji-dropdown').css("left",nLeft);
                                    } else if($(window).width() - off.left < obj.width() ){
                                        var nLeft = -1 * off.left + 10;
                                        $('.ssm-emoji-dropdown').css("left",nLeft);
                                    }
                                }
                                // Cursor position must be saved because is lost when dropdown is opened.
                                context.invoke('editor.saveRange');
                                // invoke insertText method with 'hello' on editor module.
                                //context.invoke('editor.insertText', 'EMOJI');
                            },
                        }),
                        ui.dropdown({
                            className: 'ssm-emoji-dropdown',
                            items: [
                                '  <div class="note-ext-emoji-search">',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-smile">스마일</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-hand">핸드</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-people">사람</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-nature">자연</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-object">사물</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-place">장소</button> ',
                                '   <button type="button" class="note-ext-ssm-emoji-list-hd note-ext-ssm-emoji-list-symbol">심블</button> ',
                                '  </div>',
                                '  <div class="summernote-ext-ssm-emoji-list">',
                                '     <div class="note-ext-emoji-loading">',
                                '         <i class="fa fa-spinner fa-spin fa-fw"></i> Loading...',
                                '     </div>',
                                '  </div>'
                            ].join(''),
                            callback: function ($dropdown) {
                                self.$search = $('.note-ext-emoji-search :input', $dropdown);
                                self.$list = $('.summernote-ext-ssm-emoji-list', $dropdown);
                            }
                        })
                    ]
                });

                // create jQuery object from button instance.
                var $emoji = buttonGroup.render();
                return $emoji;
            });

            // This events will be attached when editor is initialized.
            this.events = {
                // This will be called after modules are initialized.
                'summernote.init': function(we, e) {
                    // eslint-disable-next-line
                    //console.log('summernote initialized', we, e);
                    addListener();
                },
            };

            // This method will be called when editor is initialized by $('..').summernote();
            // You can create elements for plugin
            this.initialize = function() {

                var $search = self.$search;
                var $list = self.$list;
                var data = {
                    "smile":["angry.png","anguished.png","astonished.png","blush.png","bowtie.png","cold_sweat.png","confounded.png","confused.png","cry.png","disappointed.png","disappointed_relieved.png",
                        "dizzy_face.png","expressionless.png","fearful.png","flushed.png","frowning.png","grimacing.png","grin.png","grinning.png","heart_eyes.png","hushed.png","innocent.png","joy.png",
                        "kissing.png","kissing_closed_eyes.png","kissing_heart.png","kissing_smiling_eyes.png","laughing.png","mask.png","neutral_face.png","no_mouth.png","open_mouth.png","pensive.png",
                        "persevere.png","relaxed.png","relieved.png","satisfied.png","scream.png","sleeping.png","sleepy.png","smile.png","smiley.png","smirk.png","sob.png","stuck_out_tongue.png","stuck_out_tongue_closed_eyes.png",
                        "stuck_out_tongue_winking_eye.png","sunglasses.png","sweat.png","sweat_smile.png","tired_face.png","triumph.png","unamused.png","weary.png","wink.png","wink2.png","worried.png","yum.png"],
                    "hand":["+1.png","-1.png","clap.png","facepunch.png","fist.png","hand.png","ok_hand.png","open_hands.png","point_down.png","point_left.png","point_right.png","point_up.png","point_up_2.png","punch.png",
                        "raised_hand.png","raised_hands.png","thumbsdown.png","thumbsup.png","v.png","wave.png"],
                    "nature":["bear.png","cat.png","cat2.png","chicken.png","cow.png","cow2.png","crocodile.png","cyclone.png","dog.png","dog2.png","dolphin.png","dragon.png","dragon_face.png","dromedary_camel.png","earth_africa.png",
                        "earth_americas.png","earth_asia.png","ear_of_rice.png","elephant.png","fallen_leaf.png","first_quarter_moon_with_face.png","foggy.png","frog.png","full_moon.png","full_moon_with_face.png","goat.png","hamster.png",
                        "honeybee.png","horse.png","koala.png","leaves.png","leopard.png","monkey.png","monkey_face.png","mouse.png","mouse2.png","ocean.png","octocat.png","octopus.png","ox.png","panda_face.png","penguin.png","pig.png",
                        "pig2.png","poodle.png","rabbit.png","rabbit2.png","ram.png","rat.png","sheep.png","snail.png","snake.png","snowboarder.png","snowflake.png","snowman.png","sunflower.png","tiger.png","tiger2.png","tropical_fish.png",
                        "turtle.png","waning_crescent_moon.png","waning_gibbous_moon.png","water_buffalo.png","waxing_crescent_moon.png","waxing_gibbous_moon.png","whale.png","whale2.png","wolf.png","zap.png"],
                    "object":["apple.png","banana.png","car.png","cherries.png","cherry_blossom.png","chestnut.png","chocolate_bar.png","christmas_tree.png","circus_tent.png","cocktail.png","coffee.png","cookie.png","corn.png","crown.png",
                        "crystal_ball.png","curry.png","custard.png","dango.png","doughnut.png","dress.png","dvd.png","egg.png","eggplant.png","eightball.png","electric_plug.png","evergreen_tree.png","eyeglasses.png","fax.png","file_folder.png",
                        "fire_engine.png","fishing_pole_and_fish.png","fish_cake.png","flags.png","flashlight.png","floppy_disk.png","flower_playing_cards.png","football.png","fork_and_knife.png","four_leaf_clover.png","fried_shrimp.png",
                        "fries.png","fuelpump.png","game_die.png","gem.png","gift.png","grapes.png","green_apple.png","green_book.png","guitar.png","gun.png","handbag.png","headphones.png","helicopter.png","herb.png","hibiscus.png",
                        "high_brightness.png","high_heel.png","hocho.png","honey_pot.png","horse_racing.png","hotsprings.png","hourglass.png","hourglass_flowing_sand.png","icecream.png","ice_cream.png","inbox_tray.png","iphone.png",
                        "ledger.png","lemon.png","link.png","lips.png","lipstick.png","lock.png","mailbox.png","mailbox_closed.png","mailbox_with_mail.png","mailbox_with_no_mail.png","maple_leaf.png","mountain_bicyclist.png","pencil.png",
                        "pencil2.png","pill.png","pineapple.png","postbox.png","red_car.png","ribbon.png","rice.png","rice_ball.png","ring.png","rose.png","surfer.png","syringe.png","tada.png","tea.png","telephone.png",
                        "telephone_receiver.png","ticket.png","toilet.png","tophat.png","truck.png","trumpet.png","tshirt.png","tulip.png","tv.png","video_camera.png","watch.png","watermelon.png"],
                    "people":["angel.png","baby.png","blue_heart.png","bow.png","boy.png","bride_with_veil.png","broken_heart.png","construction_worker.png","cop.png","couple.png","couplekiss.png","couple_with_heart.png","crying_cat_face.png",
                        "cupid.png","dancer.png","dancers.png","eyes.png","family.png","feelsgood.png","finnadie.png","fire.png","gift_heart.png","girl.png","goberserk.png","godmode.png","green_heart.png","guardsman.png","haircut.png",
                        "heart.png","heartbeat.png","heartpulse.png","hearts.png","heart_decoration.png","heart_eyes_cat.png","hurtrealbad.png","imp.png","information_desk_person.png","joy_cat.png","kissing_cat.png","man.png",
                        "man_with_gua_pi_mao.png","man_with_turban.png","massage.png","muscle.png","nose.png","no_good.png","ok_woman.png","older_man.png","older_woman.png","person_frowning.png","person_with_blond_hair.png",
                        "person_with_pouting_face.png","pouting_cat.png","pray.png","princess.png","purple_heart.png","rage1.png","rage2.png","rage3.png","rage4.png","raising_hand.png","revolving_hearts.png","runner.png","running.png",
                        "santa.png","scream_cat.png","smiley_cat.png","smile_cat.png","smiling_imp.png","smirk_cat.png","sparkles.png","sparkling_heart.png","star.png","star2.png","stars.png","suspect.png","two_hearts.png",
                        "two_men_holding_hands.png","two_women_holding_hands.png","walking.png","woman.png","yellow_heart.png","zzz.png"],
                    "place":["church.png","convenience_store.png","department_store.png","european_castle.png","european_post_office.png","factory.png","ferris_wheel.png","fountain.png","golf.png","hospital.png","hotel.png","house.png",
                        "house_with_garden.png","japanese_castle.png","love_hotel.png","minibus.png","monorail.png","mountain_cableway.png","mountain_railway.png","office.png","post_office.png","railway_car.png","roller_coaster.png",
                        "school.png","ship.png","station.png","statue_of_liberty.png","taxi.png","tokyo_tower.png","train.png","train2.png","tram.png","trolleybus.png","wedding.png"],
                    "symbol":["1234.png","a.png","ab.png","abc.png","abcd.png","arrows_clockwise.png","arrows_counterclockwise.png","arrow_backward.png","arrow_double_down.png","arrow_double_up.png","arrow_down.png","arrow_down_small.png",
                        "arrow_forward.png","arrow_heading_down.png","arrow_heading_up.png","arrow_left.png","arrow_lower_left.png","arrow_lower_right.png","arrow_right.png","arrow_right_hook.png","arrow_up.png","arrow_upper_left.png",
                        "arrow_upper_right.png","arrow_up_down.png","arrow_up_small.png","b.png","capital_abcd.png","clock1.png","clock10.png","clock1030.png","clock11.png","clock1130.png","clock12.png","clock1230.png","clock130.png",
                        "clock2.png","clock230.png","clock3.png","clock330.png","clock4.png","clock430.png","clock5.png","clock530.png","clock6.png","clock630.png","clock7.png","clock730.png","clock8.png","clock830.png","clock9.png",
                        "clock930.png","congratulations.png","construction.png","cool.png","eight.png","end.png","exclamation.png","fast_forward.png","five.png","four.png","free.png","gemini.png","globe_with_meridians.png",
                        "grey_exclamation.png","grey_question.png","hash.png","heavy_check_mark.png","heavy_division_sign.png","heavy_dollar_sign.png","heavy_exclamation_mark.png","heavy_minus_sign.png","heavy_multiplication_x.png",
                        "heavy_plus_sign.png","id.png","ideograph_advantage.png","information_source.png","keycap_ten.png","koko.png","large_blue_circle.png","large_blue_diamond.png","large_orange_diamond.png","leftwards_arrow_with_hook.png",
                        "left_luggage.png","left_right_arrow.png","leo.png","libra.png","mens.png","metro.png","name_badge.png","negative_squared_cross_mark.png","new.png","ng.png","nine.png","non-potable_water.png","no_bicycles.png",
                        "no_entry.png","no_entry_sign.png","no_mobile_phones.png","no_pedestrians.png","no_smoking.png","o.png","o2.png","ok.png","one.png","onetwothreefour.png","parking.png","passport_control.png","potable_water.png",
                        "put_litter_in_its_place.png","question.png","recycle.png","red_circle.png","registered.png","repeat.png","repeat_one.png","restroom.png","rewind.png","sa.png","sagittarius.png","scorpius.png","secret.png",
                        "seven.png","signal_strength.png","six.png","six_pointed_star.png","small_blue_diamond.png","small_orange_diamond.png","small_red_triangle.png","small_red_triangle_down.png","sos.png","symbols.png","three.png",
                        "top.png","twisted_rightwards_arrows.png","two.png","u5272.png","u5408.png","u55b6.png","u6307.png","u6708.png","u6709.png","u6e80.png","u7121.png","u7533.png","u7981.png","u7a7a.png","uk.png","underage.png",
                        "up.png","warning.png","wc.png","wheelchair.png","white_check_mark.png","womens.png","x.png","zero.png"]
                };

                window.emojis = Object.keys(data);
                window.emojiUrls = data;

                // remove the loading icon
                $('.note-ext-emoji-loading').remove();

                $.each(window.emojiUrls, function (theme, icons) {
                    $.each(icons, function(index,icon_file){
                        var url = document.emojiSource + '/' + theme + '/' + icon_file;
                        //console.log(url);
                        setTimeout(function() { // prevents lag during DOM insertion
                            var $btn = $('<button/>',
                                {
                                    'class': 'note-emoji-btn btn btn-link',
                                    'theme': theme,
                                    'title': icon_file,
                                    'type': 'button',
                                    'tabindex': '-1'
                                });
                            var $img = $('<img/>', {'src': url});
                            //console.log(url);
                            $btn.html($img);
                            if(theme == 'smile'){
                                $btn.show();
                            } else {
                                $btn.hide();
                            }
                            $btn.click( function(event) {
                                event.preventDefault();
                                context.invoke('emoji.insertEmoji', name, url);
                            });
                            $list.append($btn);
                        }, 0); //timeout
                    });
                }); // $each

                // filter the emoji list based on current search text
                self.$search.keyup(function () {
                    self.filter($search.val());
                });
            };

            // apply search filter on each key press in search input
            this.filter = function (filter) {
                var $icons = $('button', self.$list);
                var rx_filter;

                if (filter === '') {
                    $icons.show();
                }
                else {
                    rx_filter = new RegExp(filter);
                    $icons.each(function () {
                        var $item = $(this);

                        if (rx_filter.test($item.attr('theme'))) {
                            $item.show();
                        }
                        else {
                            $item.hide();
                        }
                    });
                }
            };

            this.insertEmoji = function (name, url) {
                var img = new Image();
                img.src = url;
                img.alt = name;
                img.title = name;
                //img.className = 'emoji-img-inline';

                // We restore cursor position and element is inserted in correct pos.
                context.invoke('editor.restoreRange');
                context.invoke('editor.focus');
                context.invoke('editor.insertNode', img);
            };


            // This methods will be called when editor is destroyed by $('..').summernote('destroy');
            // You should remove elements on `initialize`.
            this.destroy = function() {
                //context.invoke('editor.insertText', 'hello end');
            };
        },
    });
}));
