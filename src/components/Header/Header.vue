<template>
	<header>
		<h3><router-link to="/"><i class="fab fa-vuejs"></i>Tube</router-link></h3>

		<div class="toggle-darkmode">
			<transition name="darkmode" mode="out-in">
				<i class="fas fa-sun fa-fw" @click="toggleDarkmode" v-if="!darkmode"></i>
				<i class="fas fa-moon fa-fw" @click="toggleDarkmode" v-else></i>
			</transition>
		</div>

		<router-link v-if="userIsAdmin" to="/admin" class="admin-area"><i class="fas fa-fw fa-user-shield"></i></router-link>

		<GoogleSignIn />
	</header>
</template>

<script>
// https://jfelix.info/blog/using-react-spring-to-animate-svg-icons-dark-mode-toggle
import { mapGetters } from 'vuex';

import GoogleSignIn from '../UI/GoogleSignIn.vue';

export default {
	components: { 
		GoogleSignIn
	},
	props: [],
	data() {
		return {
			title: process.env.VUE_APP_TITLE,
			darkmode: false,
		};
	},
	computed: {
		...mapGetters(["userIsAdmin"])
	},
	mounted(){

	},
	created(){
		this.darkmode = localStorage.getItem('darkmode');
		if(this.darkmode){
			document.body.className = 'darkmode';
		}else{
			document.body.className = '';
		}

	},
	methods: {
		toggleDarkmode(){
			this.darkmode = !this.darkmode;
			if(this.darkmode){
				document.body.className = 'darkmode';
				localStorage.setItem('darkmode', 1);
			}else{
				document.body.className = '';
				localStorage.removeItem('darkmode')
			}
		},

	}
}
</script>

<style scoped>
	header {
		align-items: center;
		background-color: #222222;
		color: #d7dadc;
		display: flex;
		padding: 10px 15px;
	}

	h3 {
		flex: 1;
		margin-bottom: 0;
		font-family: 'Roboto Condensed', sans-serif;
	}

	h3 a {
		color: #d7dadc;
		text-decoration: none;
	}

	i {
		color: #42b983;
		cursor: pointer;
		font-size: 26px;
		line-height: 32px;

		/* transform-origin: bottom center; */
	}
	
	.admin-area,
	.toggle-darkmode {
		margin-right:30px;
		overflow:hidden;
	}

	.fa-sun {
		color: #fafa60;
	}

	.fa-moon {
		color: #FDFD96;
		font-size: 22px;
	}


	span {
		position: relative;
	}

	span i {
		transition: all 0.3s;
		position: absolute;
		top: 0;
		left: 0;
	}

	button {
		background-color: transparent;
		border: 0;
		color: #d7dadc;
	}

	button:active,
	button:focus {
		outline: 0;
	}

	a {
		color: white;
	}

	/* a:hover, 
	a:active,
	a.router-link-active {
		color: var(--bs-blue);
	} */

	.darkmode-enter-from {

	}

	.darkmode-enter-active {
		animation: rotate-in 0.15s ease-out;
	}

	.darkmode-enter-to {
		
	}

	.darkmode-leave-from {
		
	}

	.darkmode-leave-active {
		animation: rotate-out 0.15s ease-out;
	}

	.darkmode-leave-to {
		
	}

	@keyframes rotate-in {
		from {
			opacity: 0;
			transform: rotate(90deg);
		}

		to {
			opacity: 1;
			transform: rotate(0deg);
		}
	}

	@keyframes rotate-out {
		from {
			opacity: 1;
			transform: rotate(0deg);
		}

		to {
			opacity: 0;
			transform: rotate(-90deg);
		}
	}

	@keyframes fade {
		from {
			opacity: 0;
			transform: scale(0);
			/* transform: translateY(50px); */
		}

		to {
			opacity: 1;
			transform: scale(1);
			/* transform: translateY(0); */
		}
	}
</style>