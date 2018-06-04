import {Component} from "@angular/core";
import {NavController} from "ionic-angular";
import {HomePage} from "../home/home";
import {SearchPage} from "../search/search";
import {ActivityPage} from "../activity/activity";
import {MyProfilePage} from "../my-profile/my-profile";

@Component({
  selector: 'page-main-tabs',
  templateUrl: 'main-tabs.html'
})
export class MainTabsPage {
  // set tabs
  public tabHome = HomePage;
  public tabSearch = SearchPage;
  public tabActivity = ActivityPage;
  public tabMe = MyProfilePage;

  constructor(public nav: NavController) {
  }
}
