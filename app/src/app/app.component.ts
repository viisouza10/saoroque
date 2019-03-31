import {Component} from '@angular/core';
import {Platform} from 'ionic-angular';
import {ViewChild} from '@angular/core';
import {StatusBar} from '@ionic-native/status-bar';
import {SplashScreen} from '@ionic-native/splash-screen';

// import pages
import {HomePage} from '../pages/home/home';
import {RegisterPage} from '../pages/register/register';
import {LoginPage} from '../pages/login/login';
import {SettingPage} from '../pages/setting/setting';
import {FindFriendPage} from '../pages/find-friend/find-friend';
import {HotelsPage} from '../pages/hotels/hotels';
import {RestaurantsPage} from '../pages/restaurants/restaurants';
import { MainTabsPage } from "../pages/main-tabs/main-tabs";
// end import pages

@Component({
  templateUrl: 'app.html',
  queries: {
    nav: new ViewChild('content')
  }
})
export class MyApp {

  public rootPage: any;

  public nav: any;

  public pages = [
    {
      title: 'Home',
      count: 0,
      component: MainTabsPage
    },

    {
      title: 'Invite Friend',
      count: 0,
      component: FindFriendPage
    },

    {
      title: 'Hotels',
      count: 0,
      component: HotelsPage
    },

    {
      title: 'Restaurants',
      count: 0,
      component: RestaurantsPage
    },

    {
      title: 'Settings',
      count: 0,
      component: SettingPage
    },

    {
      title: 'Logout',
      count: 0,
      component: LoginPage
    },

    // import menu

  ];

  constructor(public platform: Platform, statusBar: StatusBar, splashScreen: SplashScreen) {
    // this.rootPage = HomePage;
    // this.rootPage = RegisterPage;
    this.rootPage = HomePage;
    platform.ready().then(() => {
      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      statusBar.styleDefault();
      splashScreen.hide();
    });
  }

  openPage(page) {
    // Reset the content nav to have just this page
    // we wouldn't want the back button to show in this scenario
    this.nav.setRoot(page.component);
  }
}
