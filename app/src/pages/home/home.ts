import { Component } from "@angular/core";
import { App, NavController, LoadingController } from "ionic-angular";
import { GatewayService } from "../../services/gateway-service";
import { HotelService } from "../../services/hotel-service";
import { RestaurantService } from "../../services/restaurant-service";
import { AttractionService } from "../../services/attraction-service";
import { RestaurantDetailPage } from "../restaurant-detail/restaurant-detail";
import { AttractionDetailPage } from "../attraction-detail/attraction-detail";
import { HotelDetailPage } from "../hotel-detail/hotel-detail";
import { RestaurantsPage } from "../restaurants/restaurants";
import { HotelsPage } from "../hotels/hotels";
import { AttractionsPage } from "../attractions/attractions";
import { Geolocation } from "@ionic-native/geolocation";
import { WeatherProvider } from "../../providers/weather/weather";
import { EstabelecimentoProvider } from "../../providers/estabelecimento/estabelecimento";

/*
 Generated class for the LoginPage page.

 See http://ionicframework.com/docs/v2/components/#navigation for more info on
 Ionic pages and navigation.
 */
@Component({
  selector: "page-home",
  templateUrl: "home.html"
})
export class HomePage {
  // restaurants
  public restaurants: any;
  // hotels
  public hotels: any;
  // attractions
  public attractions: any;

  private hoje: Date = new Date();

  private arrayDia: Array<String> = [
    "seu <strong>Domingo</strong>",
    "<strong>sua Segunda</strong>",
    "<strong>sua Terça</strong>",
    "<strong>sua Quarta</strong>",
    "<strong>sua Quinta</strong>",
    "<strong>sua Sexta</strong>",
    "<strong>seu Sábado</strong>"
  ];

  public diaSemana: String = this.arrayDia[this.hoje.getDay()];

  public clima: String;

  public temperatura: Number;
  constructor(
    public app: App,
    public nav: NavController,
    public gatewayService: GatewayService,
    public hotelService: HotelService,
    public restaurantService: RestaurantService,
    public attractionService: AttractionService,
    public wather: WeatherProvider,
    private geolocation: Geolocation,
    private estabelecimento: EstabelecimentoProvider,
    public loading:LoadingController
  ) {
    const loader = this.loading.create({
      content: "Buscando estabelecimentos, aguarde...",
    });
    loader.present();
    this.geolocation.getCurrentPosition().then((resp) => {
      // estabelecimento.getAll({}).then(res => {      
      estabelecimento.getAll({"latitude": resp.coords.latitude,"longitude":resp.coords.longitude}).then(res => {      
        this.restaurants = res.data.restaurantes;
        this.hotels =  res.data.hoteis;
        this.attractions =  res.data.eventos;
        wather.tempo().then(res => {
          this.clima = res.condition_slug;
          this.temperatura = res.temp;
          loader.dismiss();
        });
      });
     }).catch((error) => {
       console.log('Error getting location', error);
       estabelecimento.getAll({}).then(res => {      
        this.restaurants = res.data.restaurantes;
        this.hotels =  res.data.hoteis;
        this.attractions =  res.data.eventos;
        wather.tempo().then(res => {
          this.clima = res.condition_slug;
          this.temperatura = res.temp;
          loader.dismiss();
        });
      });
     });
     
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }

  todosRestaurantes(){
    this.nav.push(RestaurantsPage,{restaurantes:this.restaurants})
  }

  todosHoteis(){
    this.nav.push(HotelsPage,{hoteis:this.hotels})
  }

  todosEventos(){
    this.nav.push(AttractionsPage,{eventos:this.attractions})
  }

  verRestaurante(restaurante) {
    this.nav.push(RestaurantDetailPage, {restaurante: restaurante})
  }

  verHotel(hotel) {
    this.nav.push(HotelDetailPage, {hotel: hotel})
  }

  verEvento(evento) {
    this.nav.push(AttractionDetailPage, {evento: evento})
  }
  
}
