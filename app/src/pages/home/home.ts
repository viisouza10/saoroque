import { Component } from "@angular/core";
import { App, NavController, LoadingController } from "ionic-angular";
import { RestaurantsPage } from "../restaurants/restaurants";
import { HotelsPage } from "../hotels/hotels";
import { Geolocation } from "@ionic-native/geolocation";
import { WeatherProvider } from "../../providers/weather/weather";
import { EstabelecimentoProvider } from "../../providers/estabelecimento/estabelecimento";
import { PlaceDetailPage } from "../place-detail/place-detail";
import { ActivityPage } from "../activity/activity";


@Component({
  selector: "page-home",
  templateUrl: "home.html"
})
export class HomePage {

  public near: any;

  public attractions: any = [];

  public movies: any;

  private hoje: Date = new Date();

  public welcomeText: String;

  private arrayDia: Array<String> = [
    "seu <strong>Domingo</strong>",
    "sua <strong>Segunda</strong>",
    "sua <strong>Terça</strong>",
    "sua <strong>Quarta</strong>",
    "sua <strong>Quinta</strong>",
    "sua <strong>Sexta</strong>",
    "seu <strong>Sábado</strong>"
  ];

  public categories: any = [];

  public diaSemana: String = this.arrayDia[this.hoje.getDay()];

  public clima: String;

  public temperatura: Number;
  constructor(
    public app: App,
    public nav: NavController,
    public wather: WeatherProvider,
    private geolocation: Geolocation,
    private estabelecimento: EstabelecimentoProvider,
    public loading: LoadingController
  ) {
    const loader = this.loading.create({
      content: "aguarde..."
    });

    if (this.hoje.getHours() >= 0 && this.hoje.getHours() <= 12) {
      this.welcomeText = "Bom Dia";
    }
    if (this.hoje.getHours() > 12 && this.hoje.getHours() <= 18) {
      this.welcomeText = "Boa Tarde";
    }
    if (this.hoje.getHours() > 18 && this.hoje.getHours() <= 24) {
      this.welcomeText = "Boa Noite";
    }

    loader.present();
    this.geolocation
      .getCurrentPosition()
      .then(resp => {
        this.estabelecimento
          .getAll("distance",true,resp.coords.latitude,resp.coords.longitude)
          .then(res => {
            console.warn("Res",res);
            this.attractions['movies'] = res[2];
            this.attractions['events'] = res[3];
            this.categories.push({
              name:"Filmes",
              icons:["ios-videocam-outline","ios-closed-captioning-outline"],
              data:res[2],
              handleAll:()=>{
                this.nav.push(ActivityPage, { attractions:this.attractions,activity:'movies' });
              },
              handle:(item)=>{
                this.nav.push(ActivityPage, { attractions:this.attractions,activity:'movies' });
              }
            })
            this.categories.push({
              name:"Eventos",
              icons:["ios-musical-note-outline","ios-microphone-outline"],
              data:res[3],
              handleAll:()=>{
                this.nav.push(ActivityPage, { attractions:this.attractions,activity:'events' });
              },
              handle:(item)=>{
                this.nav.push(ActivityPage, { attractions:this.attractions,activity:'events' });
              }
            })
            this.categories.push({
              name:"Próximos",
              icons:["ios-pin-outline","ios-navigate-outline"],
              data:res[0],
              handleAll:(itens)=>{
                this.nav.push(HotelsPage, { itens:itens });
                // TODO:mudar hotelspage
              },
              handle:(item)=>{
                this.nav.push(PlaceDetailPage, { item });
              }
            })

            wather.tempo().then(res => {
              console.warn(res);
              switch (res.condition_slug) {
                case "storm":
                  this.clima = "thunderstorm";
                  break;
                case "cloudly_night":
                  this.clima = "cloudy-night";
                  break;
                case "clear_night":
                  this.clima = "moon";
                  break;
                case "cloudly_day":
                  this.clima = "sunny";
                  break;

                default:
                  this.clima = res.condition_slug
                  break;
              }

              this.temperatura = res.temp;
              loader.dismiss();
            });
          });
      })
      .catch(error => {
        console.log("Error getting location", error);
        // estabelecimento.getAll({}).then(res => {
        //   this.restaurants = res.data.restaurantes;
        //   this.hotels = res.data.hoteis;
        //   this.attractions = res.data.eventos;
        //   this.movies = res.data.filmes;
        //   wather.tempo().then(res => {
        //     this.clima = res.condition_slug;
        //     this.temperatura = res.temp;
        //     loader.dismiss();
        //   });
        // });
      });
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
