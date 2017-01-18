import java.io.*;



public class ren13_02{
	
	public static void main(String[] args)throws IOException{
		//BufferedReader br =new BufferedReader(new InputStreamReader(System.in));
		
		String carName[] = new String[3]; 
		carName[0] = "普通車"; 
		carName[1] = "トラック"; 
		carName[2] = "タクシー";
		
		Car car[] = new Car[3];
		car[0]  = new Car(15.5,40.0);
		car[1]  = new Truck(4000,5.0,60.0);
		car[2]  = new Taxi(300,10.0,50.0);
		
		System.out.println();
		System.out.println("各車を満タンにします");
		
		for(int i=0; i < car.length ;i++){
			car[i].addFuel();    //class carのメソッド
			System.out.print(carName[i]+"の");
			car[i].displayFuel();//class carのメソッド
		}
		
		System.out.println();
		System.out.println("60キロ走行します");
		
		for(int i=0; i < car.length ;i++){
			car[i].run(60.0);
			System.out.print(carName[i]+"の");
			car[i].displayFuel();
		}
		
		//Truckのイベント
		System.out.println("1000キロの荷物を積む");
		//サブクラスのメソッドの使用はできないのでキャスト
		//Truck truck = (Track)car[1];
		((Truck)car[1]).loadGoods(1000);
		((Truck)car[1]).displayAmount();
		System.out.println();
		
		//Taxiのイベント
		Taxi taxi = (Taxi)car[2];
		taxi.displayFare();//Taxiのメソッド
		System.out.println("タクシーの料金をリセットします");
		taxi.resetFare(); //Taxiのメソッド
		taxi.displayFare();//Taxiのメソッド
	}
}
//Animal a = new Cat();
//↑の場合　変数はサブクラス優先
//メソッドは下記
//スーパーあり　サブなし　⇒　スーパー優先
//スーパーなし　サブあり　⇒　キャストするべし　((Cat)a).display();
//スーパーあり　サブあり　⇒　サブ　（オーバーライド）
