import {Component} from "@angular/core";
import {NavController, Platform, NavParams} from "ionic-angular";


declare var google: any;

@Component({
  selector: 'page-movie-detail',
  templateUrl: 'movie-detail.html'
})
export class MovieDetailPage {
  // attraction info
  public movie: any;
  // default rating
  public rating = 0;
  // Map
  public map: any;
  // rating values
  public ratingValues = [0, 0, 0, 0, 0];

  constructor(public nav: NavController, public platform: Platform,public navParams: NavParams) {
    // // set sample data
    // this.movie = attractionService.getItem(1);
    this.movie = navParams.get("filme");
    console.log(this.movie);
    
    // // process reviews data
    // for (let key in this.movie.reviews) {
    //   this.ratingValues[this.movie.reviews[key].rating - 1]++;
    // }
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
    let latLng = new google.maps.LatLng(this.movie.latitude, this.movie.longitude);

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
