import { Injectable } from "@angular/core";
import { SimplejsProvider } from "../../providers/simplejs/simplejs";
import { Http, URLSearchParams } from '@angular/http';

@Injectable()
export class EstabelecimentoProvider {
  private restaurants: any;
  private key:string = "AIzaSyAd7C7vzuFsMGxPrvDMMVpqQdu1iyCwxuA";
  private url: string = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-23.5490258,-47.1140443&opennow&rankby=distance&keyword=bar%20restaurante%20sao%20roque&type=food&key=${this.key}`;
  // private url: string = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=-23.5490258,-47.1140443&radius=500000&type=market&keyword=estancia%20sao%20roque&key=${this.key}`;
  constructor(public SIMPLEJS: SimplejsProvider, public http: Http) {

  }

  
  getAll(lat:number,long:number,type:string,place_id:number = 0,city:string="São Roque") {
    return this.http
    .get(this.url)
    .timeout(10000)
    .map((res) => res)
    .toPromise()
    .then((sucess) => {
      var data = JSON.parse((<any>sucess)._body).results;
      data.forEach(element => {
        console.log(element);
        
        var img = element.photos ? element.photos[0] : "";
        element.photo = `https://maps.googleapis.com/maps/api/place/photo?maxwidth=250&maxheight=156&photoreference=${img.photo_reference}&sensor=false&key=${this.key}`;
      });
      return {restaurantes:data};
    })
    .catch((err) => {
      if (err.name == "TimeoutError") {
        err.mensagem = "Ocorreu uma falha de comunicação, tente novamente mais tarde ou verifique sua conexão com a internet"
      }
      return err
    })
  }

  getItem(id) {
    for (var i = 0; i < this.restaurants.length; i++) {
      if (this.restaurants[i].id === parseInt(id)) {
        return this.restaurants[i];
      }
    }
    return null;
  }

  remove(item) {
    this.restaurants.splice(this.restaurants.indexOf(item), 1);
  }
}