const express = require('express')
const app = express()
app.use(express.static('public'))

const fetchApi = require('node-fetch')

const dotenv = require('dotenv')
dotenv.config()

const { TwingEnvironment, TwingLoaderFilesystem } = require('twing')
let twing = new TwingEnvironment(new TwingLoaderFilesystem('./templates'))

const bodyParser = require('body-parser');
app.use(bodyParser.urlencoded({ extended: true }));

app.get('/', (req, res) => {
  fetchApi(`${process.env.HOST_URL}/api/matches`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': process.env.HOST_URL
    },
    body: JSON.stringify({
      apiKey: process.env.API_KEY
    })
  }).then(response => {
    response
      .json()
      .then(json => {
        twing.render('index.twig', { matches: json.data }).then((output) => {
          res.end(output);
        });
      })
    ;
  });
})

app.get('/delete/:matchId', (req, res) => {
  fetchApi(`${process.env.HOST_URL}/api/delete-match`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': process.env.HOST_URL
    },
    body: JSON.stringify({
      apiKey: process.env.API_KEY,
      matchId: parseInt(req.params.matchId)
    })
  }).then(response => {
    response
      .json()
      .then(res.redirect(302, '/'))
    ;
  });
})

app.get('/add', (req, res) => {
  fetchApi(`${process.env.HOST_URL}/api/teams`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': process.env.HOST_URL
    },
    body: JSON.stringify({
      apiKey: process.env.API_KEY
    })
  }).then(response => {
    response
    .json()
    .then(json => {
      twing.render('add.twig', { names: json.data }).then((output) => {
        res.end(output);
      });
    })
    ;
  });
})

app.post('/add', (req, res) => {
  fetchApi(`${process.env.HOST_URL}/api/add-match`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': process.env.HOST_URL
    },
    body: JSON.stringify({
      apiKey: process.env.API_KEY,
      match: {
        playDate: new Date(req.body.play_date),
        homeTeam: req.body.home_team,
        homeScore: parseInt(req.body.home_score),
        awayTeam: req.body.away_team,
        awayScore: parseInt(req.body.away_score)
      }
    })
  }).then(response => {
    response
      .json()
      .then(res.redirect(302, '/'))
    ;
  });
})

app.listen(3000)