import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";

@Component({
  selector: 'page-activity',
  templateUrl: 'activity.html'
})
export class ActivityPage {
  // activities
  public activity:string = "movies";
  public movies: any;
  public events: any;

  constructor(public nav: NavController,private navParams:NavParams) {
    console.warn(this.navParams.data);
    this.activity = this.navParams.data.activity;
    this.movies = this.navParams.data.attractions.movies;
    this.events = this.navParams.data.attractions.events;    
  }
  setActivity(act){
    console.warn("act",act);
    
    this.activity = act;
  }

}
