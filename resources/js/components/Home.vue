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
        <button @click="testMe">Click me</button>
        <b-table striped hover :items="filteredMovieToShow"></b-table>
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
            firstMovies: []
        };
    },
    methods: {
        testMe() {
            console.log(this.moviesToShow);
        },

        newTitleSearch() {
            fetch(`http://www.omdbapi.com/?s=${this.name}&apikey=7c9b80bf`)
                .then(response => response.json())
                .then(response => this.addMoviesToDatabase(response.Search));
        },

        addMoviesToDatabase(moviesToAdd) {
            var params = {
                data: moviesToAdd
            };

            axios.post("/api/search", params).then(function(response) {
                console.log(response.data);
            });

            axios.get("/api/movies").then(response => {
                this.items = response.data;
            });
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
                console.log(response.data);
            });
        },
        saveFilteredTitles($titleArray) {
            this.filteredResults = $titleArray;
        }
    },

    computed: {
        input: {
            get() {
                this.newTitleSearch();
                return this.name;
            },
            set(val) {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.name = val;
                }, 500);
            }
        },
        moviesToShow() {
            return this.items.filter(movie =>
                movie.name.toLowerCase().includes(this.name.toLowerCase())
            );
        },

        filteredMovieToShow() {
            return this.moviesToShow.filter(
                movie => this.filteredResults.indexOf(movie.name) >= 0
            );
        }
    },
    watch: {
        name: function() {
            this.newTitleSearch();
        }
    },

    mounted: function() {
        this.filterGenreLanguage();
        axios.get("/api/movies").then(response => {
            this.items = response.data;
        });
    }
};
</script>
