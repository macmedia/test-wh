/* =============================================================
 * bootstrap-slider.js v1.0.0
 * ETCHamac.com
 * =============================================================
 * Copyright 2017 ETCHamac
 * Code modified from https://codepen.io/seanstopnik/pen/CeLqA
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

var rangeSlider = function(){
  var slider = $('.range-slider'),
      range  = $('.range-slider__range'),
      value  = $('.range-slider__value'),
			label  = $('.range-slider__label'),
			msg    = $('.range_msg'),
			minus  = $('.range-slider__minus'),
			add    = $('.range-slider__add');

  slider.each(function(){

    value.each(function(){
      var value = $(this).parent().prev('input').attr('value');
      $(this).html(value);
    });

		add.on('click', function(){
			var value = parseInt($(this).parent().prev('input').val()) + 1;
			$(this).parent().prev('input').val(value);
			$(this).parent().prev('input').trigger('input');
		});

		minus.on('click', function(){
			var value = parseInt($(this).parent().prev('input').val()) - 1;
			$(this).parent().prev('input').val(value);
			$(this).parent().prev('input').trigger('input');
		});

    range.on('input', function(){
			$(this).next().children().next('.range-slider__value').html(this.value);

			if (this.value >= 1000){
				msg.show();
			}else{
				msg.hide();
			}
    });
  });
};

rangeSlider();
