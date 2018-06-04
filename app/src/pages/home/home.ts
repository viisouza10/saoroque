import { Component } from "@angular/core";
import { App, NavController } from "ionic-angular";
import { GatewayService } from "../../services/gateway-service";
import { HotelService } from "../../services/hotel-service";
import { RestaurantService } from "../../services/restaurant-service";
import { AttractionService } from "../../services/attraction-service";
import { RestaurantDetailPage } from "../restaurant-detail/restaurant-detail";
import { HotelDetailPage } from "../hotel-detail/hotel-detail";
import { RestaurantsPage } from "../restaurants/restaurants";
import { HotelsPage } from "../hotels/hotels";
import { AttractionsPage } from "../attractions/attractions";
import { Geolocation } from '@ionic-native/geolocation';
import { WeatherProvider } from "../../providers/weather/weather";

/*
 Generated class for the LoginPage page.

 See http://ionicframework.com/docs/v2/components/#navigation for more info on
 Ionic pages and navigation.
 */
@Component({
  selector: "page-home",
  templateUrl: "home.html"
})
export class HomePage {
  // restaurants
  public restaurants: any;
  // hotels
  public hotels: any;
  // attractions
  public attractions: any;

  private hoje:Date = new Date();

  private arrayDia:Array<String> = ['seu <strong>Domingo</strong>','<strong>sua Segunda</strong>','<strong>sua Terça</strong>','<strong>sua Quarta</strong>','<strong>sua Quinta</strong>','<strong>sua Sexta</strong>','<strong>seu Sábado</strong>'];

  public diaSemana:String = this.arrayDia[this.hoje.getDay()];

  public clima:String;

  public temperatura:Number;
  constructor(
    public app: App,
    public nav: NavController,
    public gatewayService: GatewayService,
    public hotelService: HotelService,
    public restaurantService: RestaurantService,
    public attractionService: AttractionService,
    public wather: WeatherProvider,
    private geolocation: Geolocation
  ) {
    
    


    wather.tempo().then(res =>{
      console.log(res);
      
      this.clima = res.condition_slug;
      this.temperatura = res.temp;
      
    })  
  

    // set sample data
    this.restaurants = restaurantService.getAll();
    this.hotels = hotelService.getAll();
    this.attractions = attractionService.getAll();
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }

  // view restaurant detail
  viewRestaurant(id) {
    this.app.getRootNav().push(RestaurantDetailPage, { id: id });
  }

  // view hotel detail
  viewHotel(id) {
    this.app.getRootNav().push(HotelDetailPage, { id: id });
  }

  // view attraction detail
  viewAttraction(id) {
    this.app.getRootNav().push(AttractionsPage, { id: id });
  }

  // view all restaurants
  viewAllRestaurants() {
    this.app.getRootNav().push(RestaurantsPage);
  }

  // view all hotels
  viewAllHotels() {
    this.app.getRootNav().push(HotelsPage);
  }

  // view all restaurants
  viewAllAttractions() {
    this.app.getRootNav().push(AttractionsPage);
  }
}
