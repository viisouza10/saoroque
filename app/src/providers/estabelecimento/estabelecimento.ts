import { Injectable } from "@angular/core";
import { SimplejsProvider } from "../../providers/simplejs/simplejs";
import { Http, URLSearchParams } from '@angular/http';

@Injectable()
export class EstabelecimentoProvider {
  private restaurants: any;
  private key: string = "AIzaSyAd7C7vzuFsMGxPrvDMMVpqQdu1iyCwxuA";
  private url: string = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=${this.key}&`;

  constructor(public SIMPLEJS: SimplejsProvider, public http: Http) {

  }


  getAll(rankby: string = "distance", opennow: boolean = true, lat: number = -23.5294771, long: number = -47.138349, city: string = "São Roque") {
    return Promise.all(
      [
        this.getCat("food", "restaurante", rankby, opennow, lat, long, city),
        this.getCat("lodging", "hotel", rankby, opennow, lat, long, city),
        // this.getCat("night club", false, rankby, opennow, lat, long, city)
      ]
    )
  }

  async getCat(type, keyword, rankby, opennow, lat, long, city) {
    var data = [];
    var url = this.url;

    if(type)data['type'] = type;
    if(keyword)data['keyword'] = keyword;
    data['location'] = `${lat.toString()},${long.toString()}`;
    data['rankby'] = rankby;

    url += Object.keys(data).map((key) => {
      return encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
    }).join('&');

    if (opennow) url += "&opennow";
    console.warn(url);
    
    return new Promise((resolve, reject) => {
      this.http
        .get(url)
        .timeout(10000)
        .map((res) => res)
        .toPromise()
        .then((sucess) => {
          var data = JSON.parse((<any>sucess)._body).results;
          data.forEach(element => {
            var img = element.photos ? element.photos[0] : "";
            element.photo = img.photo_reference ?`https://maps.googleapis.com/maps/api/place/photo?maxwidth=250&maxheight=156&photoreference=${img.photo_reference}&sensor=false&key=${this.key}` : 'assets/img/default_place.png';
          });
          resolve(data);
        })
        .catch((err) => {
          if (err.name == "TimeoutError") {
            err.mensagem = "Ocorreu uma falha de comunicação, tente novamente mais tarde ou verifique sua conexão com a internet"
          }
          reject(err)
        })
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