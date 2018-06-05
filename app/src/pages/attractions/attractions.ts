import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";
import {AttractionService} from "../../services/attraction-service";
import {AttractionDetailPage} from "../attraction-detail/attraction-detail";

/*
 Generated class for the LoginPage page.

 See http://ionicframework.com/docs/v2/components/#navigation for more info on
 Ionic pages and navigation.
 */
@Component({
  selector: 'page-attractions',
  templateUrl: 'attractions.html'
})
export class AttractionsPage {
  // list of attractions
  public attractions;

  constructor(public nav: NavController, public attractionService: AttractionService,public navParams: NavParams) {
    this.attractions = navParams.get("eventos");
  }

  verEvento(evento) {
    this.nav.push(AttractionDetailPage, {evento: evento})
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
