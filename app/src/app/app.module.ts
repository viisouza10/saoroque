import { NgModule } from "@angular/core";
import { IonicApp, IonicModule } from "ionic-angular";
import { MyApp } from "./app.component";
import { BrowserModule } from "@angular/platform-browser";
import { StatusBar } from "@ionic-native/status-bar";
import { SplashScreen } from "@ionic-native/splash-screen";
import { HttpClientModule } from "@angular/common/http";
import { HttpModule } from "@angular/http";
import { Network } from "@ionic-native/network";
import { Geolocation } from '@ionic-native/geolocation';

import {
  FileTransfer
} from "@ionic-native/file-transfer";

// import pages
import { FindFriendPage } from "../pages/find-friend/find-friend";
import { HomePage } from "../pages/home/home";
import { PlaceDetailPage } from "../pages/place-detail/place-detail";
import { HotelsPage } from "../pages/hotels/hotels";
import { LoginPage } from "../pages/login/login";
import { MainTabsPage } from "../pages/main-tabs/main-tabs";
import { ModalFilterPage } from "../pages/modal-filter/modal-filter";
import { MyProfilePage } from "../pages/my-profile/my-profile";
import { RegisterPage } from "../pages/register/register";
import { RestaurantsPage } from "../pages/restaurants/restaurants";
import { SearchPage } from "../pages/search/search";
import { SettingPage } from "../pages/setting/setting";
import { WelcomePage } from "../pages/welcome/welcome";
import { SimplejsProvider } from "../providers/simplejs/simplejs";
import { WeatherProvider } from '../providers/weather/weather';
import { EstabelecimentoProvider } from '../providers/estabelecimento/estabelecimento';
import { RestaurantService } from "../services/restaurant-service";
import { HotelService } from "../services/hotel-service";
import { AttractionService } from "../services/attraction-service";
import { ActivityPage } from "../pages/activity/activity";
// end import pages

@NgModule({
  declarations: [
    MyApp,
    FindFriendPage,
    ActivityPage,
    HomePage,
    PlaceDetailPage,
    HotelsPage,
    LoginPage,
    MainTabsPage,
    ModalFilterPage,
    MyProfilePage,
    RegisterPage,
    RestaurantsPage,
    SearchPage,
    SettingPage,
    WelcomePage
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    HttpModule,
    IonicModule.forRoot(MyApp,{
      backButtonText: '',
      modalEnter: 'modal-slide-in',
      modalLeave: 'modal-slide-out',
      tabsPlacement: 'bottom',
      pageTransition: 'ios-transition'
    })
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp,
    FindFriendPage,
    ActivityPage,
    HomePage,
    PlaceDetailPage,
    HotelsPage,
    LoginPage,
    MainTabsPage,
    ModalFilterPage,
    MyProfilePage,
    RegisterPage,
    RestaurantsPage,
    SearchPage,
    SettingPage,
    WelcomePage
  ],
  providers: [
    StatusBar,
    SplashScreen,
    RestaurantService,
    HotelService,
    Network,
    Geolocation,
    SimplejsProvider,
    FileTransfer,
    WeatherProvider,
    AttractionService,
    EstabelecimentoProvider
    /* import services */
  ]
})
export class AppModule {}
