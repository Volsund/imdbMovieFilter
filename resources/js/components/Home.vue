<template>
    <div>
        <b-card bg-variant="light">
            <b-form-group
                label-cols-lg="3"
                label="Search for movie:"
                label-size="lg"
                label-class="font-weight-bold pt-0"
            >
                <b-form-group
                    label-cols-sm="3"
                    label="Name:"
                    label-align-sm="right"
                    label-for="nested-name"
                >
                    <b-form-input
                        v-model="input"
                        id="nested-name"
                    ></b-form-input>
                </b-form-group>

                <b-form-group
                    label-cols-sm="3"
                    label="Genre:"
                    label-align-sm="right"
                    label-for="nested-genre"
                >
                    <b-form-input
                        v-model="genre"
                        id="nested-genre"
                        @change="filterGenreLanguage()"
                    ></b-form-input>
                </b-form-group>

                <b-form-group
                    label-cols-sm="3"
                    label="Language:"
                    label-align-sm="right"
                    label-for="nested-language"
                >
                    <b-form-input
                        v-model="language"
                        id="nested-language"
                        @change="filterGenreLanguage()"
                    ></b-form-input>
                </b-form-group>
            </b-form-group>
        </b-card>
        <h3 v-if="filteredMovieToShow.length > 0">Search Results:</h3>
        <b-table
            bordered
            head-variant="dark"
            small
            striped
            hover
            :items="filteredMovieToShow"
        ></b-table>

        <h3 v-if="historyLog.length > 0">History Log:</h3>
        <b-table
            bordered
            head-variant="dark"
            small
            id="history-table"
            striped
            hover
            :items="historyLog"
        ></b-table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            genre: "",
            language: "",
            items: [],
            filteredResults: [],
            timeout: 0,
            historyLog: []
        };
    },
    methods: {
        newTitleSearch() {
            //Getting all movies with associated title input.
            fetch(`http://www.omdbapi.com/?s=${this.name}&apikey=7c9b80bf`)
                .then(response => response.json())
                .then(response => this.addMoviesToDatabase(response.Search));
        },
        //Method to add movies from first api call to database.
        addMoviesToDatabase(moviesToAdd) {
            var params = {
                data: moviesToAdd
            };

            axios.post("/api/search", params).then(function(response) {
                console.log(response.data);
            });

            //Getting all movies from database.
            axios.get("/api/movies").then(response => {
                this.items = response.data;
            });

            //Making filter api call so filtered titles are updated
            this.filterGenreLanguage();

            //Getting new search history
            this.getHistory();
        },

        filterGenreLanguage() {
            let self = this;

            var params = {
                data: {
                    language: this.language,
                    genre: this.genre,
                    name: this.name
                }
            };
            axios.post("/api/filter", params).then(function(response) {
                //Receiving array of filtered movie titles and assigning to component variable.
                self.filteredResults = response.data;
                // console.log(response.data);
                console.log("filter ok");
            });
            this.getHistory();
        },
        //Method to get all search history data from database
        getHistory() {
            axios.get("/api/history").then(response => {
                this.historyLog = response.data;
                // console.log(response.data);
            });
        }
    },

    computed: {
        //seting timout on input change
        input: {
            get() {
                return this.name;
            },
            set(val) {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.name = val;
                }, 500);
            }
        },

        //Filtering all movies depending on title input box.
        moviesToShow() {
            return this.items.filter(movie =>
                movie.name.toLowerCase().includes(this.name.toLowerCase())
            );
        },

        //Filtering api response array with all titles against moviesToShow which checks what use is typing into title box
        filteredMovieToShow() {
            return this.moviesToShow.filter(
                movie => this.filteredResults.indexOf(movie.name) >= 0
            );
        }
    },
    watch: {
        //title input box watcher that makes new title request when user stops writting for 0.5 seconds
        name: function() {
            this.newTitleSearch();
        }
    },

    //Methods to call when page is loaded for the first time
    mounted: function() {
        this.getHistory();
        this.filterGenreLanguage();
        //Getting all the movies from database on page load
        axios.get("/api/movies").then(response => {
            this.items = response.data;
        });
    }
};
</script>

<style lang="css">
h3 {
    padding-top: 20px;
}
</style>
