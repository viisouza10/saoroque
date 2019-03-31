import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";

@Component({
  selector: 'page-restaurants',
  templateUrl: 'restaurants.html'
})
export class RestaurantsPage {
  // list of restaurants
  public restaurants;

  constructor(public nav: NavController,public navParams: NavParams) {
    console.log(navParams.get("restaurantes"));
    
    this.restaurants = navParams.get("restaurantes");
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
