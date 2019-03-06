import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";
import {RestaurantService} from "../../services/restaurant-service";
import {RestaurantDetailPage} from "../restaurant-detail/restaurant-detail";

@Component({
  selector: 'page-restaurants',
  templateUrl: 'restaurants.html'
})
export class RestaurantsPage {
  // list of restaurants
  public restaurants;

  constructor(public nav: NavController, public restaurantService: RestaurantService,public navParams: NavParams) {
    console.log(navParams.get("restaurantes"));
    
    this.restaurants = navParams.get("restaurantes");
  }

  // view restaurant detail
  verRestaurante(restaurante) {
    this.nav.push(RestaurantDetailPage, {restaurante: restaurante})
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
