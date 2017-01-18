<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="assets/css/all.css">
    <title>UNCHAIN -We are freelancer</title>
</head>
<body>


<header>
    <div class="g_navi">

        <h1 class="logo"><img src="assets/images/logo.png" height="46" width="48" alt="UNCHAIN"></h1>

        <nav>
            <ul class="nav--elm">
                <li><a href="#works">WORKS</a></li>
                <li><a href="#member">MENBER</a></li>
                <li><a href="#article">ARTICLE</a></li>
            </ul>
        </nav>

         <div class="sns">
            <ul>

                <!-- Facebook -->   
                 <li class="share-facebook icon icons-icon_fb">
                <a  class="js-insert-selflink" href="https://www.facebook.com/sharer/sharer.php?u=" target="_blank">Facebook</a>
                </li>

                <!-- Twitter -->
                <li class="share-twitter icon icons-icon_twitter">
                <a class="js-insert-selflink" href="http://twitter.com/home?status=" target="_blank">Twitter</a>
                </li>
 
            </ul>
        </div>

    </div>
</header>


<main>
    <h2 class="title">私たちのサービスを、体験してみてください。</h2>
    <div class="title-image"><img src="assets/images/title.png" height="416" width="786" alt="we are freelancer!"></div>
    <a class="inquiry js-modal" href="#modal-contact">お問い合わせ</a>

    <p class="expl">ウェブデザイナー・グラッフィクデザイナー・エンジニア・フォトグラファーなど幅広い分野で、<br class="sp-none">
専門知識や経験を持つフリーランサーと共に、低単価で高品質なサービスを目指しています。<br>
ディレクターが進行管理からクオリティチェックを行い、お客様の案件・ご要望にあったメンバーをアサインいたします。</p>


    <div class="article cf" id="article">
        <div class="elm web">
            <h3 class="type--ttl">Web</h3>
            <p class="type--text">型には込めこんだ「モノ売り」では無く、ソリューション（課題の解決）型デザインとして、各種ウェブサイトの企画設計をご相談から納品まで、一貫サポートします。</P>
        </div>

        <div class="elm graphic">
            <h3 class="type--ttl">Graphic Design</h3>
            <p class="type--text">各種媒体広告、エディトリアル、パッケージ等の企画〜デザイン、数々の印刷物全般のグラッフィクデザイン、オリジナルの写真集、作品集を作成するオーダーメイド編集も行います。</P>
        </div>

        <div class="elm photograph">
            <h3 class="type--ttl">Photograph</h3>
            <p class="type--text">写真撮影、映像制作を企画立案から画像処理、編集まで一貫して行います。モデル、ロケーション、建築インテリア、料理、広告写真撮影に関するマネージメント、そして確かなクオリティを誠意を持って提供いたします。</P>
        </div>

        <div class="elm planning">
            <h3 class="type--ttl">Planning</h3>
            <p class="type--text">企業・自治体・各種団体等が主催する、新商品や新サービスのプロモーション企画・イベント企画等の立案から事前準備、実施までをワンストップで請け負います。</P>
        </div>
    </div>

    <div id="works" class="works template">
        <p class="works--ttl">Works</h3>
        <div class="details cf js-details"></div>
        <div id="paging-works"></div><div class="pager"></div>
    </div>

    <div id="member" class="member template">
        <p class="member--ttl">Member</h3>
        <div class="details cf js-details"></div>
        <div id="paging-member"></div><div class="pager"></div>
    </div>
</main>


<footer>
    <div class="logo_area"><img class="footer_logo" src="assets/images/logo.png" height="46" width="48"></div>
    <copy class="copy">Copyright © Unchain. All Rights Reserved.</cppy>
</footer>


<!--お問い合わせ-->
<div id="modal-contact" class="contact">
    <div class="inner">
        <p class="contact--ttl">私たち、フリーランサーに関心をお持ちいただきまして、ありがとうございます。<br class="sp-none">
            ご意見・ご相談、制作費用のお見積もりなど、お気軽にお問い合わせください。<br class="sp-none">
            お問い合わせ内容の確認後、ディレクターより連絡をさせていただきます。</p>
            <form id="sendmail" method="post">
                <div class="">
                    <label for="name">お名前</label>
                    <p class="required">【必須】</p>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="">
                    <label for="email">メールアドレス</label>
                    <p class="required">【必須】</p>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="">
                    <label for="company">会社名</label>
                    <input class="no_req" id="company" name="company"></input>
                </div>

                <div class="">
                    <label for="tel">電話番号</label>
                    <input class="no_req" id="tel" name="tel"></input>
                </div>

                <div class="">
                    <label for="article">ご依頼内容</label>
                    <p class="required">【必須】</p>

                    <div class="article">
                        <label><input required type="radio" name="article" value="ウェブサイト">ウェブサイト</label>
                        <label><input required type="radio" name="article" value="グラフィックデザイン">グラフィックデザイン</label>
                        <label><input required type="radio" name="article" value="写真撮影">写真撮影</label>
                        <label><input required type="radio" name="article" value="その他">その他</label>
                    </div>

                </div>


                <div class="">
                    <label for="budget">ご予算</label>
                    <p class="required">【必須】</p>
                    <input id="budget" name="budget" required></input>
                </div>


                <div class="">
                    <label for="delivery">納期</label>
                    <p class="required">【必須】</p>
                    <input id="delivery" name="delivery" required></input>
                </div>



                <div class="">
                    <label for="purpose">目的・概要</label>
                    <p class="required">【必須】</p>
                    <textarea id="purpose" name="purpose" required></textarea>
                </div>




                <div class="btn_area">
                    <button type="submit" id="submit" class="submit">メッセージを送信</button>
                </div>
            </form>
    </div>
</div>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="assets/js/lib/jquery.min.js"><\/script>')</script>
<script src="assets/js/lib/libs.js"></script>
<script src="assets/js/common.js"></script>
</body>
</html>

