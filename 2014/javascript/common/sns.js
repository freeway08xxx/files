
'use strict';

function snsSet(type,layout){
	var $snsWrap = $('.j-t-sns');
	var $facebook = $('.j-t-sns-facebook');
	var $twitter = $('.j-t-sns-twitter');
	var $line = $('.j-t-sns-line');

	$snsWrap.css({'overflow': 'hidden','margin': '10px auto','display': '-webkit-box'});
	$facebook.css({'margin': '0 10px 0 0'});
	$twitter.css({'margin': '0 10px 0 0'});

	$(window).load(function() {
		setTimeout(function(){
			var boxTwitter = '<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="vertical">ツイート</a>';
			var buttonTwitter = '<a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja">ツイート</a>';

			var boxFacebookShare = '<div class="fb-share-button" data-href="" data-type="box_count"></div>';
			var buttonFacebookShare = '<div class="fb-share-button" data-href="" data-type="button_count"></div>';

			var boxFacebookLike = '<div class="fb-like" data-layout="box_count" data-href="" data-action="like" data-show-faces="false" data-share="false"></div>';
			var buttonFacebookLike = '<div class="fb-like" data-layout="button_count" data-href="" data-action="like" data-show-faces="false" data-share="false"></div>';

			if(layout == 'box'){
				$snsWrap.css({'width':'200px', 'height':'62px'});
				$twitter.append(boxTwitter);
				if(type == 'share'){
					$facebook.append(boxFacebookShare);
				}else{
					$facebook.append(boxFacebookLike);
				}
			}else{
				$snsWrap.css({'width':'300px','height':'20px'});
				$line.css({'margin':'0 0 0 -40px'});
				$twitter.append(buttonTwitter);
				if(type == 'share'){
					$facebook.append(buttonFacebookShare);
				}else{
					$facebook.append(buttonFacebookLike);
				}
			}

			//facebook
			(function(d, s, id){
				var fjs = d.createElement(s);
				if(d.getElementById(id)) return;
				fjs.id = id;
				fjs.src = "http://connect.facebook.net/ja_JP/all.js#xfbml=1";
				$facebook.append(fjs);
			}(document, 'script', 'facebook-jssdk'));

			//twitter
			!function(d,s,id){
				var tjs,p=/^http:/.test(d.location)?'http':'https';
				if(!d.getElementById(id)){
					tjs=d.createElement(s);
					tjs.id=id;
					tjs.src=p+'://platform.twitter.com/widgets.js';
					$twitter.append(tjs);
				}
			}(document, 'script', 'twitter-wjs');

			$snsWrap.css('visibility','visible');

  	  },1000);
	});
}