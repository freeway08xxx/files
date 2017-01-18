<div class="alert alert-warning attention toggle-collapse"
 ng-click="mediacost.models.is_collapse_attention = !mediacost.models.is_collapse_attention">

	<h5>
		<strong>媒体費個別設定について</strong>
	</h5>

	<div class="top_20px" collapse="mediacost.models.is_collapse_attention">
		<p>
			媒体費個別設定とは、Falconレポートのみに適用される媒体費設定です。<br>
			新規レポート作成時またはテンプレート編集時の媒体費項目よりも、細かい単位で設定することが出来ます。<br>
			（こちらの個別設定が優先的に適用されます）<br><br>
			<strong><font color="red">設定単位はアカウント・媒体の2種類</font></strong>で、<strong><font color="red">クライアントごと</font></strong>の設定になります。<br>
			※期間やテンプレートを分けての登録は出来ません。<br>
			※アカウント・媒体で重複した内容が設定されている場合、アカウントで設定した媒体費が優先的に適用されます。
		</p>

		<hr />

		<div class="example">
			【設定例】<br>
			例１）特定のアカウントのみ、媒体費が異なる場合。<br>
			　　　└テンプレート編集時に媒体費20%に設定、個別設定で「アカウントID：123456789」を15%に設定。<br>
			　　　└「アカウントID：123456789」は15%、それ以外の実績は20%で計上されます。<br>
			<br>
			例２）特定のアカウントと媒体の媒体費が異なる場合。<br>
			　　　└テンプレート編集時に媒体費20%に設定、個別設定で「YDNアカウントID：123456789」を15%、「媒体名：YDN」を10%に設定。<br>
			　　　└「YDNアカウントID：123456789」は15%、左記以外の「媒体名：YDN」のアカウントは10%、それ以外の実績は20%で計上されます。<br>
			<br>
			<strong>※合算値が入る箇所(合計シートや合計行)などはそれぞれを合算した値となります。</strong>
		</div>
	</div>
</div>