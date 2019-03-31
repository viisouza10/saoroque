import { Component } from "@angular/core";
import { NavController, Platform, NavParams } from "ionic-angular";

declare var google: any;

@Component({
  selector: 'page-place-detail',
  templateUrl: 'place-detail.html'
})
export class PlaceDetailPage {
  // item info
  public item: any;
  public tipo: String;

  // default rating
  public rating = 0;
  public theme: String;

  // Map
  public map: any;

  constructor(public nav: NavController, public platform: Platform, public navParams: NavParams) {
    this.item = navParams.get("item");
    console.log("item",this.item);
    // this.tipo = navParams.get("tipo");
    this.theme = `primary`;
  }

  ionViewDidLoad() {
    // init map
    this.initializeMap();
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }

  // rate function
  rate(stars) {
    this.rating = stars;
  }

  initializeMap() {
    let latLng = new google.maps.LatLng(this.item.geometry.location.lat, this.item.geometry.location.lng);

    let mapOptions = {
      center: latLng,
      zoom: 16,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: false,
      zoomControl: false,
      streetViewControl: false
    }

    this.map = new google.maps.Map(document.getElementById("map-detail"), mapOptions);
    new google.maps.Marker({
      map: this.map,
      animation: google.maps.Animation.DROP,
      position: this.map.getCenter()
    });

    // refresh map
    setTimeout(() => {
      google.maps.event.trigger(this.map, 'resize');
    }, 300);
  }
}
