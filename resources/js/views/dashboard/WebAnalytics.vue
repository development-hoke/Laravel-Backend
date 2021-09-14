<template>
	<div>
		<v-container fluid pb-0>
			<visitors-area-chart></visitors-area-chart>
		</v-container>
		<v-container fluid grid-list-xl>
			<v-row class="stats-card-v4">
				<v-col xl="4" lg="4" md="4" sm="4" cols="12" b-50>
					<stats-card-v4 
						:title="$t('message.totalPageViews')"
						value="35,455"
						body="+5% from yesterday"
						:status="1"
						:data="[20,15,12,6,18,17,20,2]"
						:labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']"
						:color="ChartConfig.color.info"
					>
					</stats-card-v4>
				</v-col>
				<v-col xl="4" lg="4" md="4" sm="4" cols="12" b-50>
					<stats-card-v4 
						:title="$t('message.totalEarnings')"
						value="$4,500"
						body="-2% from yesterday"
						:status="0"
						:data="[15,20,6,9,18,17,20,2]"
						:labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']"
						:color="ChartConfig.color.warning"
					></stats-card-v4>
				</v-col>
				<v-col xl="4" lg="4" md="4" sm="4" cols="12" b-50>
					<stats-card-v4 
						:title="$t('message.impressions')"
						value="45,000"
						body="+5% from yesterday"
						:status="1"
						:data="[1,15,12,6,18,17,20,22]"
						:labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug']"
						:color="ChartConfig.color.danger"
					></stats-card-v4>
				</v-col>
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.activityAroundWorld')"
					:fullScreen="true"
					:closeable="true"
					:reloadable="true"
					colClasses="col-xl-8 col-lg-8 col-md-7 col-sm-6 col-12"
					contentCustomClass="pt-0"
				>
					<activity-around-world></activity-around-world>
				</app-card>
				<app-card
					:heading="$t('message.deviceSeparations')"
					:fullScreen="true"
					:closeable="true"
					:reloadable="true"
					colClasses="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-12"
					customClasses="device-seperation-wrap"
				>
					<device-separations></device-separations>
				</app-card>
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.browserStatics')"
					:fullBlock="true"
					:fullScreen="true"
					:closeable="true"
					:reloadable="true"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
				>
					<browser-statics></browser-statics>
				</app-card>
				<app-card
					:heading="$t('message.trafficChannel')"
					:fullScreen="true"
					:closeable="true"
					:reloadable="true"
					:footer="true"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					contentCustomClass="pt-0"
				>
					<traffic-channel-v2></traffic-channel-v2>
					<span class="grey--text d-custom-flex align-items-center justify-content-end" slot="footer">
						<i class="zmdi zmdi-replay mr-2"></i>
						<span class="fs-12 fw-normal">{{$t('message.updated10MinAgo')}}</span>
					</span>
				</app-card>
				<app-card
					:heading="$t('message.serverLoad')"
					:fullScreen="true"
					:closeable="true"
					:reloadable="true"
					colClasses="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12"
					contentCustomClass="pt-0 h-100"
				>
					<server-load styles="width: 100%; height: 275px;"></server-load>
				</app-card>
			</v-row>
			<v-row>
				<app-card 
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12"
					contentCustomClass="pb-0 px-0"
				>
					<bandwidth-usage-v2></bandwidth-usage-v2>
				</app-card>
				<app-card 
					contentCustomClass="pb-0 px-0"
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12"
				>
					<total-downloading></total-downloading>
				</app-card>
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.notifications')"
					:fullBlock="true"
					:closeable="true"
					:reloadable="true"
					:fullScreen="true"
					:footer="true"
					colClasses="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12"
				>
					<notifications></notifications>
					<div class="d-custom-flex justify-space-between" slot="footer">
						<v-btn small color="primary">View All</v-btn>
						<span class="grey--text d-custom-flex align-items-center">
							<i class="zmdi zmdi-replay mr-2"></i> 
							<span class="fs-12 fw-normal">Updated 10 min ago</span>
						</span>
					</div>
				</app-card>
				<app-card
					:heading="$t('message.supportTickets')"
					:fullBlock="true"
					:closeable="true"
					:reloadable="true"
					:fullScreen="true"
					:footer="true"
					colClasses="col-xl-8 col-lg-8 col-md-6 col-sm-12 col-12"
				>
					<support-tickets></support-tickets>
					<div class="d-custom-flex justify-space-between" slot="footer">
						<v-btn small color="primary">View All</v-btn>
						<span class="grey--text d-custom-flex align-items-center">
							<i class="zmdi zmdi-replay mr-2"></i> 
							<span class="fs-12 fw-normal">Updated 10 min ago</span>
						</span>
					</div>
				</app-card>
			</v-row>
			<v-row>
				<v-col xl="3" lg="3" md="6" sm="12" cols="6" class="stats-widget-v3">
					<stats-card-v3
						icon="bookmark"
						:title="$t('message.totalBookmarked')"
						value="25,700"
						color="primary"
					></stats-card-v3>
				</v-col>
				<v-col xl="3" lg="3" md="6" sm="6" cols="6" class="stats-widget-v3">
					<stats-card-v3
						icon="cloud_download"
						:title="$t('message.itemsDownloads')"
						value="14,200"
						color="success"
					></stats-card-v3>
				</v-col>
				<v-col xl="3" lg="3" md="6" sm="6" cols="6" class="stats-widget-v3">
					<stats-card-v3
						icon="cloud_upload"
						:title="$t('message.itemsUploaded')"
						value="5,400"
						color="error"
					></stats-card-v3>
				</v-col>
				<v-col xl="3" lg="3" md="6" sm="6" cols="6" class="stats-widget-v3">
					<stats-card-v3
						icon="insert_chart"
						:title="$t('message.totalAnalytics')"
						value="1,288"
						color="info"
					></stats-card-v3>
				</v-col>		
			</v-row>
			<v-row>
				<app-card
					:heading="$t('message.hotKeywords')"
					:fullBlock="true"
					:closeable="true"
					:reloadable="true"
					:fullScreen="true"
					:footer="true"
					colClasses="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
				>
					<hot-keywords></hot-keywords>
					<div slot="footer">
						<span class="grey--text d-custom-flex align-items-center">
							<i class="zmdi zmdi-replay mr-2"></i> 
							<span class="fs-12 fw-normal">Updated 10 min ago</span>
						</span>
					</div>
				</app-card>
				<v-col xl="6" lg="6" md="6"  cols="12">
					<v-row>
						<v-col xl="6" lg="6" md="6"  cols="12" class="d-block">
							<users-stat></users-stat>
						</v-col>
						<v-col xl="6" lg="6" md="6"  cols="12" class="d-block">
							<platform-users-stat></platform-users-stat>
						</v-col>
						<app-card
							:heading="$t('message.serverStatus')"
							colClasses="col-xl-12 col-lg-12 col-md-12 col-12 d-xs-half-block w-full"
							:closeable="true"
							:reloadable="true"
							:fullScreen="true"
							:footer="true"
							contentCustomClass="py-0"
						>
							<server-status></server-status>
							<div slot="footer">
								<span class="grey--text d-custom-flex align-items-center">
									<i class="zmdi zmdi-replay mr-2"></i> 
									<span class="fs-12 fw-normal">Updated 10 min ago</span>
								</span>
							</div>
						</app-card>
					</v-row>
				</v-col>
			</v-row>
			<v-row>
				<v-col xl="6" lg="6" md="6" cols="12" >
					<customer-support-service></customer-support-service>
				</v-col>
				<v-col xl="6" lg="6" md="6" cols="12" >
					<promo-widget></promo-widget>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script>
import VisitorsAreaChart from "Components/Widgets/VisitorsAreaChart";
import TrafficChannelV2 from "Components/Widgets/TrafficChannelV2";
import BrowserStatics from "Components/Widgets/BrowserStatics";
import ServerLoad from "Components/Widgets/ServerLoad";
import BandwidthUsageV2 from "Components/Widgets/BandwidthUsageV2";
import TotalDownloading from "Components/Widgets/TotalDownloading";
import Notifications from "Components/Widgets/Notifications";
import SupportTickets from "Components/Widgets/SupportTickets";
import HotKeywords from "Components/Widgets/HotKeywords";
import UsersStat from "Components/Widgets/UsersStat";
import PlatformUsersStat from "Components/Widgets/PlatformUsersStat";
import ServerStatus from "Components/Widgets/ServerStatus";
import CustomerSupportService from "Components/Widgets/24x7CustomerSupport";
import PromoWidget from "Components/Widgets/PromoWidget";
import ActivityAroundWorld from "Components/Widgets/ActivityAroundWorld";
import DeviceSeparations from "Components/Widgets/DeviceSeprations";

// components
import StatsCardV3 from "Components/StatsCardV3/StatsCardV3";
import StatsCardV4 from "Components/StatsCardV4/StatsCardV4";

// chart config
import { ChartConfig } from "Constants/chart-config";

export default {
  components: {
    VisitorsAreaChart,
    TrafficChannelV2,
    BrowserStatics,
    ServerLoad,
    BandwidthUsageV2,
    TotalDownloading,
    Notifications,
    SupportTickets,
    HotKeywords,
    UsersStat,
    PlatformUsersStat,
    ServerStatus,
    CustomerSupportService,
    PromoWidget,
    StatsCardV3,
    ActivityAroundWorld,
    DeviceSeparations,
    StatsCardV4
  },
  data() {
    return {
      ChartConfig
    };
  }
};
</script>
