import { createStore } from 'vuex'; 

import channelModules from './channels/index';
import videoModules from './videos/index';
import userModules from './users/index';
import formModule from './forms/index';

const store = createStore({
	modules: {
		channels: channelModules,
		videoModules: videoModules,
		users: userModules,
		forms: formModule,
	},
	state() {
		return {
			domain: '',
			toastOptions: {
				position: "bottom-center", // top-center?
				timeout: 3000,
				closeOnClick: true,
				pauseOnFocusLoss: true,
				pauseOnHover: true,
				draggable: true,
				draggablePercent: 0.6,
				showCloseButtonOnHover: false,
				hideProgressBar: true,
				closeButton: "button",
				icon: true,
				rtl: false
			}
		};
	},
	getters: {
		getSubdomain(){
			const host = window.location.host;
			const parts = host.split('.');
			return parts[0];
		},
		getToastOptions(state){
			return state.toastOptions;
		}
	},
	actions: {
		setDomain(context) {
			context.commit('setDomain', this.getSubdomain());
		},
	},
	mutations: {
		setDomain(state, payload) {
			state.domain = payload;
		},
	}
});

export default store;