class Truck extends Car{
	 //private int amountMax; //最大積載量
	 private int amount;  //現在のトラックの積載量
	 
	 //コンストラクタは返り値なし
	 //最大積載量 燃費　燃料
	 public Truck(int aMax,double fRatio,double fMax){
		 //燃費　燃料計算はスーパークラスのプライベート変数を使う
		 //このクラス内では積み降ろし機能のみ拡張
		 super(fRatio,fMax);
		 System.out.print("Truckのコンストラクタ");
		 amount += 0;
	 }	
	 
	 public void loadGoods(int a){
		 amount += a; 
	 }
	 
	 public void unloadGoods(int a){
		 amount -= a; 
	 }

	 public void displayAmount(){
		 System.out.println("現在の積載量は" + amount + "kgです");
	 }		 
}




