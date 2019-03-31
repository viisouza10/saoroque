import {Component} from "@angular/core";
import {NavController} from "ionic-angular";

@Component({
  selector: 'page-find-friend',
  templateUrl: 'find-friend.html'
})
export class FindFriendPage {
  // contacts
  public contacts;

  constructor(public nav: NavController) {
    // set sample data
  }
}
