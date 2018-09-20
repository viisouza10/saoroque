import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import 'rxjs/add/operator/map';
/*
  Generated class for the CadastroProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class CadastroProvider {
  data: any;
  constructor(public http: HttpClient) {
    console.log('Hello CadastroProvider Provider');
  }

  cadastrar(obj) {
    if (this.data) {
      return Promise.resolve(this.data);
    }
    console.log(obj);
    

    return new Promise(resolve => {
      
      this.http.post("http://saoroque.mac/api/teste",JSON.stringify(obj))
        .map(res => console.log(res))
        // .map(res => res.json())
        .subscribe(data => {
          this.data = data;
          resolve(this.data);
        });
    });
  }


}
