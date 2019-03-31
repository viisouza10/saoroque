import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";
import { PlaceDetailPage } from "../place-detail/place-detail";

/*
 Generated class for the LoginPage page.

 See http://ionicframework.com/docs/v2/components/#navigation for more info on
 Ionic pages and navigation.
 */
@Component({
  selector: 'page-hotels',
  templateUrl: 'hotels.html'
})
export class HotelsPage {
  // list of hotels
  public itens;

  constructor(public nav: NavController,public navParams :NavParams) {    
    this.itens = navParams.get("itens");
  }

  // view hotel detail
  open(item) {
    this.nav.push(PlaceDetailPage, { item });
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
