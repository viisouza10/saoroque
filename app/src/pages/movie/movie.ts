import {Component} from "@angular/core";
import {NavController, NavParams} from "ionic-angular";
import {MovieDetailPage} from "../movie-detail/movie-detail";

@Component({
  selector: 'page-movie',
  templateUrl: 'movie.html'
})
export class MoviePage {
  // list of movies
  public movies;

  constructor(public nav: NavController,public navParams: NavParams) {
    console.log(navParams.get("filmes"));
    
    this.movies = navParams.get("filmes");
  }

  // view movie detail
  verFilme(movie) {
    this.nav.push(MovieDetailPage, {filme: movie})
  }

  // make array with range is n
  range(n) {
    return new Array(Math.round(n));
  }
}
