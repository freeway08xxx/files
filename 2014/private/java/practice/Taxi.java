class Taxi extends Car{
	 private int taxiFare; // 料金
	 private int taxiRate;  // /km
	//コンストラクタ	  
	 public Taxi(int tRate,double fRatio,double fMax){
			super(fRatio,fMax);	
			System.out.println(tRate);
			taxiRate = tRate;
	 }
	 
	 //resetFare
	 public void resetFare(){
	 	taxiFare = 0;
	 }
	 
	 //override 
	 public void run(double distance){
	 	//燃費計算はスーパークラスで
		 super.run(distance);
	 	taxiFare += taxiRate * distance;
	 }	 
	 
	 //現在の料金
	 public void displayFare(){
	 	System.out.println("現在の料金は"+taxiFare+"です");
	 }
}

