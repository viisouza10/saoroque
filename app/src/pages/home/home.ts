import { Component } from "@angular/core";
import { App, NavController, LoadingController } from "ionic-angular";
import { GatewayService } from "../../services/gateway-service";
import { RestaurantDetailPage } from "../restaurant-detail/restaurant-detail";
import { AttractionDetailPage } from "../attraction-detail/attraction-detail";
import { HotelDetailPage } from "../hotel-detail/hotel-detail";
import { MovieDetailPage } from "../movie-detail/movie-detail";
import { MoviePage } from "../movie/movie";
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
  // movies
  public movies: any;

  private hoje: Date = new Date();

  private arrayDia: Array<String> = [
    "seu <strong>Domingo</strong>",
    "sua <strong>Segunda</strong>",
    "sua <strong>Terça</strong>",
    "sua <strong>Quarta</strong>",
    "sua <strong>Quinta</strong>",
    "sua <strong>Sexta</strong>",
    "seu <strong>Sábado</strong>"
  ];

  public diaSemana: String = this.arrayDia[this.hoje.getDay()];

  public clima: String;

  public temperatura: Number;
  constructor(
    public app: App,
    public nav: NavController,
    public gatewayService: GatewayService,
    public wather: WeatherProvider,
    private geolocation: Geolocation,
    private estabelecimento: EstabelecimentoProvider,
    public loading: LoadingController
  ) {
    const loader = this.loading.create({
      content: "aguarde..."
    });
    loader.present();
    this.geolocation
      .getCurrentPosition()
      .then(resp => {
        // estabelecimento.getAll({}).then(res => {
        this.estabelecimento
          .getAll(
            resp.coords.latitude,
            resp.coords.longitude,
            'market'
          )
          .then(res => {
            console.table(res);
            this.restaurants = res.restaurantes;
            // this.hotels = res.data.hoteis;
            // this.attractions = res.data.eventos;
            // this.movies = res.data.filmes;

            wather.tempo().then(res => {
              this.clima = res.condition_slug;
              this.temperatura = res.temp;
              loader.dismiss();
            });
          });
      })
      .catch(error => {
        console.log("Error getting location", error);
        estabelecimento.getAll({}).then(res => {
          this.restaurants = res.data.restaurantes;
          this.hotels = res.data.hoteis;
          this.attractions = res.data.eventos;
          this.movies = res.data.filmes;
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

  todosFilmes() {
    this.nav.push(MoviePage, { filmes: this.movies });
  }

  todosRestaurantes() {
    this.nav.push(RestaurantsPage, { restaurantes: this.restaurants });
  }

  todosHoteis() {
    this.nav.push(HotelsPage, { hoteis: this.hotels });
  }

  todosEventos() {
    this.nav.push(AttractionsPage, { eventos: this.attractions });
  }

  verFilme(filme) {
    this.nav.push(MovieDetailPage, { filme: filme });
  }

  verRestaurante(restaurante) {
    this.nav.push(RestaurantDetailPage, { restaurante: restaurante });
  }

  verHotel(hotel) {
    this.nav.push(HotelDetailPage, { hotel: hotel });
  }

  verEvento(evento) {
    this.nav.push(AttractionDetailPage, { evento: evento });
  }
}
