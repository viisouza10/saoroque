import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';

@Injectable()
export class WeatherProvider {
  data :any;
  constructor(public http: HttpClient) {
    
  }
  tempo() {
    
    return this.http
      .get("https://api.hgbrasil.com/weather/?format=json-cors&woeid=461192")
      .timeout(10000)
      .map((res) => res)
      .toPromise()
      .then((sucess) => { 
        return (<any>sucess).results;
      })
      .catch((err) => {

        if (err.name == "TimeoutError" || (err.status == 0 && err.type == 3)) {
          err.mensagem = "Ocorreu uma falha de comunicação, tente novamente mais tarde ou verifique sua conexão com a internet"
        }
        return err
      })


  }

}
