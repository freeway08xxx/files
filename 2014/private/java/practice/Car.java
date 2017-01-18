class Car{
	 private double fuelRest, fuelRatio, fuelMax; 
	 public Car(double local_fuelRest,double local_fuelRatio,double local_fuelMax){
		fuelRest   = local_fuelRest;
		fuelRatio  = local_fuelRatio;
		fuelMax    = local_fuelMax;
	 }

	 //overload　引数二つ
	 public Car(double fRatio,double fMax){
	 	fuelRest   = fMax;
		fuelRatio  = fRatio;
		fuelMax    = fMax;
		//System.out.println("overload!! 引数２つ 満タン40LL");
	 }

	 //overload 引数なし
	 public Car(){
	 	fuelRest   = 40.0;
		System.out.println("overload2!! 引数なし 満タン40LL");
	 }	 
	 
	 public void displayFuel(){
	 	System.out.println("現在の燃料は"+fuelRest +"リットルです");
	 }
	 
	 public void addFuel(double fuel){
	 	fuelRest += fuel;
		if(fuelRest>fuelMax){
			fuelRest = fuelMax;
		}
	 }
	 
	//overload 引数なし
	public void addFuel(){
		//System.out.println("overload3!! メソッドのオーバーロード");
	 	fuelRest = fuelMax;
	 }
	
	 public void run(double distance){
	 		double runRest = distance / fuelRatio;
			fuelRest = fuelRest - runRest;
	 }
	 
	  //getter
 	 public double getFuelRest(){
	 		return this.fuelRest;		
	 }
	 
 	 public double getFuelRatio(){
	 		return fuelRatio;
	 }
	 
 	 public double getFuelMax(){
	 		return this.fuelMax;
	 }	 	 
	 
}

