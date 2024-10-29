(function($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     * 
     */


    var app = angular.module('admin_user_app', ['ui.bootstrap', 'ngAnimate', 'datatables'], function($interpolateProvider) {
        $interpolateProvider.startSymbol('<#');
        $interpolateProvider.endSymbol('#>');
    });

    


    app.controller('admin_user_controller', function($scope, $http, $timeout, $compile, $rootScope, $element, $animate, DTOptionsBuilder, filterFilter) {

        $scope.dtOptions_users = {
            pageLength: 100,
            lengthMenu: [
                [100, 300, 500, -1],
                [100, 300, 500, "All"]
            ],
            order: [0, 'asc'],
            scrollY: 550,
            scrollX: true
        }


        $scope.aus_init_args = function() {

            $scope.aus_search_in_progress = 0;

            $scope.aus_chunk_key = Math.floor(Math.random() * 1000) + 10;

            $scope.aus_process_status = "new";

            $scope.aus_progress_val = "0%";

            $scope.aus_progress_verbiage = "";

            // return the total users 
            // use for progress bar
            $scope.aus_total_users = 0;

            // store all received users per post request
            // use this for OFFSET 
            // pass aus_current_users.length for offset
            $scope.aus_current_users = [];

            

        }

        $scope.aus_init_args();

        $scope.initialize_limits = function() {

            $scope.aus_limit_search = 10000;
            $scope.aus_limit_chunk = 5000;
            $scope.aus_limit_chunk_decrementor = 1000;

        }

        $scope.initialize_limits();

        



        // execute via keypress enter 
        $("#search_user_key").on('keypress', function(e) {
            if (e.which == 13) {
                $scope.search_users_exec();
            } // e.which 13
        });

        // execute via btn click
        $scope.search_users_btn = function() {
            $scope.search_users_exec();
        }

        // call main search user function, reset values
        $scope.search_users_exec = function() {

            if ($scope.search_keywords) {
                $scope.aus_init_args();
                
                $scope.aus_progress_verbiage = "Searching...";
                $scope.search_users();
            } else {
                Swal.fire(
                    'Oops!',
                    'Please enter something.',
                    'warning'
                )
            }
        }

        $scope.aus_progress_bar_provider = function() {
            var perc = ($scope.aus_current_users.length / $scope.aus_total_users) * 100;
            perc = perc.toFixed(0);
            perc = perc.toString();
            $scope.aus_progress_val = perc + '%';
            console.log("progress bar at " + $scope.aus_progress_val);
            $scope.$apply();
        }

        

        $scope.search_users = function() {

            var datas = new FormData();

            datas.append('action', 'prefix_post_search_users');
            datas.append('aus_keyword', $scope.search_keywords);
            datas.append('aus_chunk_key', $scope.aus_chunk_key);

            datas.append('aus_total_users', $scope.aus_total_users);

            datas.append('aus_limit_search', $scope.aus_limit_search);
            datas.append('aus_limit_chunk', $scope.aus_limit_chunk);

            datas.append('aus_current_users_count', $scope.aus_current_users.length)
            datas.append('aus_process_status', $scope.aus_process_status);

            

            jQuery.ajax({
                url: search_users_url.url,
                type: 'post',
                data: datas,
                processData: false,
                contentType: false,
                success: function(response) {

                    $scope.aus_new_process = 0;

                    response = JSON.parse(response);
                    console.log(response);

                    $timeout(function() {

                        $scope.aus_process_status = response.aus_process_status;

                        // succes search do a chunk process next
                        if ($scope.aus_process_status == "new") {
                            // create progress bar 
                            // show total users found 
                            if (response.total_users > 0) {

                                $scope.aus_progress_verbiage = "Found results: " + response.total_users;
                                $scope.aus_process_status = "chunking";
                                $scope.aus_total_users = response.total_users;
                                $scope.aus_search_in_progress = 1;

                                $scope.aus_limit_chunk = response.aus_limit_chunk;

                                $scope.$apply();
                                $scope.aus_progress_bar_provider();
                                console.log("found results: " + $scope.aus_total_users);
                                $scope.search_users();

                            } else {
                                $scope.aus_progress_verbiage = "No result found.";
                            }

                        } else {

                            if (response.users.length > 0) {
                                // continue chunking process until current total == total.
                                $scope.aus_current_users.push(...response.users);
                                $scope.aus_limit_chunk = response.aus_limit_chunk;
                                $scope.$apply();

                                console.log("current users: " + $scope.aus_current_users.length);
                                console.log("total users: " + $scope.aus_total_users);

                                $scope.aus_progress_bar_provider();
                                $scope.search_users();
                            } else {
                                // done
                                $scope.aus_search_in_progress = 0;
                                $scope.aus_progress_val = "0%";
                                $scope.aus_progress_verbiage = "";

                                

                            }
                        }

                    });

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    // if timeout occur increment the divider
                    console.log(errorThrown);
                    $scope.aus_limit_chunk -= $scope.aus_limit_chunk_decrementor;
                    $scope.search_users();
                }
            }); //jQuery.ajax
        }

    });

})(jQuery);