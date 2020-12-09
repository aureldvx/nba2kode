const express = require('express')
const app = express()

const {TwingEnvironment, TwingLoaderFilesystem} = require('twing')
let twing = new TwingEnvironment(new TwingLoaderFilesystem('./templates'))

app.use(express.static('public'))

app.get('/', (req, res) => {
  res.send('Hello World')
  // twing.render('index.twig', {'name': 'World'}).then((output) => {
  //   res.end(output);
  // });
})

app.delete('/', (req, res) => {
  res.send('traitement de la suppression')
})

app.get('/add', (req, res) => {
  res.send('new match here')
})

app.post('/add', (req, res) => {
  res.send('traitement de la création')
})

app.listen(3000)