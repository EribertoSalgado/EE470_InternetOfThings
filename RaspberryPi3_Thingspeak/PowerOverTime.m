% Replace this with channel ID to read data from:
readChannelID = 2755734;

% Enter your Read API Key between the '' below:
readAPIKey = '5DLCY5YLE8EJTXM9';

%% Read Data %%
% Ensure you are reading from the correct fields and the channel has data
data = thingSpeakRead(readChannelID, 'ReadKey', readAPIKey, 'NumPoints', 20);

% Check if data is returned
if isempty(data)
    disp('No data received from ThingSpeak');
else
    % Assuming Field1 is CPU usage (%) and Field2 is Temperature (Celsius)
    cpuData = data(:,1); % CPU data in Field1 (adjust as per your channel configuration)
    tempData = data(:,2); % Temperature data in Field2 (adjust as per your channel configuration)

    %% Analyze Data %%
    % Calculate average temperature and standard deviation of temperature
    avgTemp = mean(tempData);
    disp(['Average Temperature: ', num2str(avgTemp)]);

    stdTemp = std(tempData);
    disp(['Standard Deviation of Temperature: ', num2str(stdTemp)]);

    %% Calculate POWER_in_W for each data point %%
    % POWER_in_W = (0.23*100*CPU% + Temp_in_C*0.32)
    powerInW = (0.23 * 100 * cpuData + tempData * 0.32);

    %% Plot Data on One Figure %%
    figure;

    % Plot Calculated Power Consumption (in Watts)
    plot(powerInW, 'm-', 'LineWidth', 2);
    xlabel('Time (in data points)');
    ylabel('Power (W)');
    title('Calculated Power Consumption Over Time');
    grid on;
end
