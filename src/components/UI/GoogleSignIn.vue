<template>
	<a id="signin-button" @click="googleLogin" v-if="! getGoogleUser"><img src="@/assets/images/google.svg" width="24" height="24" /></a>
	<router-link to="/account" v-else><span class="account-image"><img :src="getGoogleUser.photoURL" width="32" height="32" /></span></router-link>
</template>


<script>
// TODO: We get a crappy "vuetube.app,bullshit" name with this google auth. That is garbage and I hate it. Try following some of the steps below to change that
// https://firebase.google.com/docs/auth/web/google-signin#customizing-the-redirect-domain-for-google-sign-in
//https://stackoverflow.com/questions/47945851/change-display-name-for-firebase-google-auth-provider

// We are ONLY using the auth module so we only need to import that here
import firebase from "firebase/app";
import 'firebase/auth'

import { mapGetters } from 'vuex';

export default {
  components: {  },
	props: [],
	data() {
		return {
		};
	},
	computed: {
		...mapGetters(["getGoogleUser"])
	},
	methods: {
		googleLogin(){
			const provider = new firebase.auth.GoogleAuthProvider();

			firebase.auth().signInWithPopup(provider).then((result) => {
				this.$store.dispatch('login', result);
			}).catch((err) => {
				// TODO: Should we add a toast here? Or some other error response?
				console.log("Error: "+ err.message)
			});
		},
	},
}
</script>

<style scoped>

	#signin-button {
		background-color: white;
		border-radius: 50%;
		line-height: 1;
		padding: 4px;
		cursor: pointer;
	}

	.account-image img {
		border: 2px solid #818384;
		border-radius: 50%;
	}
</style>